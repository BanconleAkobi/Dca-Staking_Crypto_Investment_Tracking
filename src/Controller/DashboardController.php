<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\TransactionType;
use App\Repository\CryptoRepository;
use App\Repository\TransactionRepository;
use App\Service\PortfolioCalculatorService;
use App\Service\CryptoApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    public function __construct(
        private PortfolioCalculatorService $portfolioCalculator,
        private CryptoApiService $cryptoApiService
    ) {}

    #[Route('/', name: 'root', methods: ['GET'])]
    public function root(): Response { return $this->redirectToRoute('app_dashboard'); }

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(Request $req, CryptoRepository $cryptos, TransactionRepository $txRepo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $portfolioData = $this->portfolioCalculator->calculatePortfolio($user);
        $stats = $this->portfolioCalculator->getPortfolioStats($portfolioData);
        
        // Get recent transactions
        $recentTransactions = $txRepo->findBy(
            ['user' => $user],
            ['date' => 'DESC'],
            5
        );

        $form = $this->createForm(TransactionType::class);

        return $this->render('dashboard/index.html.twig', [
            'portfolio_data' => $portfolioData,
            'stats' => $stats,
            'recent_transactions' => $recentTransactions,
            'form' => $form->createView(),
        ]);
    }
}
