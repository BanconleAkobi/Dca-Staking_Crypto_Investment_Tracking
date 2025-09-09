<?php

namespace App\Controller;

use App\Entity\Crypto;
use App\Form\CryptoType;
use App\Repository\CryptoRepository;
use App\Service\SubscriptionAccessService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/crypto')]
class CryptoController extends AbstractController
{
    #[Route('/', name: 'app_crypto_index', methods: ['GET'])]
    public function index(CryptoRepository $cryptoRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $cryptos = $cryptoRepository->findBy(['user' => $user]);

        return $this->render('crypto/index.html.twig', [
            'cryptos' => $cryptos,
        ]);
    }

    #[Route('/new', name: 'app_crypto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SubscriptionAccessService $subscriptionAccess): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        
        if (!$subscriptionAccess->canAddCrypto($user)) {
            $this->addFlash('error', $subscriptionAccess->getUpgradeMessage('crypto'));
            return $this->redirectToRoute('app_billing_index');
        }
        
        $crypto = new Crypto();
        $crypto->setUser($user);
        $form = $this->createForm(CryptoType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($crypto);
            $entityManager->flush();

            $this->addFlash('success', 'Cryptomonnaie ajoutée avec succès !');
            return $this->redirectToRoute('app_crypto_index');
        }

        return $this->render('crypto/new.html.twig', [
            'crypto' => $crypto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crypto_show', methods: ['GET'])]
    public function show(Crypto $crypto): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        return $this->render('crypto/show.html.twig', [
            'crypto' => $crypto,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_crypto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Crypto $crypto, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $form = $this->createForm(CryptoType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Cryptomonnaie modifiée avec succès !');
            return $this->redirectToRoute('app_crypto_index');
        }

        return $this->render('crypto/edit.html.twig', [
            'crypto' => $crypto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crypto_delete', methods: ['POST'])]
    public function delete(Request $request, Crypto $crypto, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        if ($this->isCsrfTokenValid('delete'.$crypto->getId(), $request->request->get('_token'))) {
            $entityManager->remove($crypto);
            $entityManager->flush();
            
            $this->addFlash('success', 'Cryptomonnaie supprimée avec succès !');
        }

        return $this->redirectToRoute('app_crypto_index');
    }
}
