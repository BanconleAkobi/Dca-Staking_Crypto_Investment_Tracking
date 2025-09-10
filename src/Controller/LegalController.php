<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegalController extends AbstractController
{
    #[Route('/legal/terms', name: 'app_legal_terms')]
    public function terms(): Response
    {
        return $this->render('legal/terms.html.twig', [
            'page_title' => 'Conditions Générales d\'Utilisation',
            'page_subtitle' => 'InvestFlow - CGU'
        ]);
    }

    #[Route('/legal/privacy', name: 'app_legal_privacy')]
    public function privacy(): Response
    {
        return $this->render('legal/privacy.html.twig', [
            'page_title' => 'Politique de Confidentialité',
            'page_subtitle' => 'InvestFlow - Protection des données'
        ]);
    }

    #[Route('/legal/disclaimer', name: 'app_legal_disclaimer')]
    public function disclaimer(): Response
    {
        return $this->render('legal/disclaimer.html.twig', [
            'page_title' => 'Avertissement et Prévention',
            'page_subtitle' => 'InvestFlow - Informations importantes'
        ]);
    }

    #[Route('/legal/refund', name: 'app_legal_refund')]
    public function refund(): Response
    {
        return $this->render('legal/refund.html.twig', [
            'page_title' => 'Politique de Remboursement',
            'page_subtitle' => 'InvestFlow - Conditions de remboursement'
        ]);
    }

    #[Route('/legal/contact', name: 'app_legal_contact')]
    public function contact(): Response
    {
        return $this->render('legal/contact.html.twig', [
            'page_title' => 'Contact et Support',
            'page_subtitle' => 'InvestFlow - Nous contacter'
        ]);
    }
}
