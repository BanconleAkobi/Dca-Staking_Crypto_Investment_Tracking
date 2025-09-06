<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\TransactionType;
use App\Repository\CryptoRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/', name: 'root', methods: ['GET'])]
    public function root(): Response { return $this->redirectToRoute('app_dashboard'); }

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(Request $req, CryptoRepository $cryptos, TransactionRepository $txRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $symbol = strtoupper((string)$req->query->get('crypto', 'BTC'));
        $crypto = $cryptos->findOneBy(['symbol' => $symbol]);
        if (!$crypto) { throw $this->createNotFoundException('Unknown crypto'); }

        $transactions = $txRepo->findForUserAndCryptoChrono($this->getUser(), $crypto);
        $form = $this->createForm(TransactionType::class);

        return $this->render('dashboard/index.html.twig', [
            'crypto' => $symbol,
            'transactions' => $transactions,
            'form' => $form->createView(),
        ]);
    }
}
