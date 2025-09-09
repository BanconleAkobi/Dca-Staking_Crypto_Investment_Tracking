<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    #[Route('/', name: 'app_settings_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $currentTheme = $request->getSession()->get('theme', 'light');
        
        if ($request->isMethod('POST')) {
            $theme = $request->request->get('theme', 'light');
            $request->getSession()->set('theme', $theme);
            
            $this->addFlash('success', 'Paramètres sauvegardés avec succès !');
            return $this->redirectToRoute('app_settings_index');
        }

        return $this->render('settings/index.html.twig', [
            'current_theme' => $currentTheme,
            'user' => $user
        ]);
    }

    #[Route('/theme/{theme}', name: 'app_settings_theme', methods: ['POST'])]
    public function changeTheme(Request $request, string $theme): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        if (in_array($theme, ['light', 'dark', 'auto'])) {
            $request->getSession()->set('theme', $theme);
        }

        return $this->json(['success' => true, 'theme' => $theme]);
    }
}
