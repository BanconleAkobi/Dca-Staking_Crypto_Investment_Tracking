<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\TransactionType;
use App\Repository\CryptoRepository;
use App\Repository\TransactionRepository;
use App\Repository\AssetRepository;
use App\Repository\SavingsAccountRepository;
use App\Repository\WithdrawalRepository;
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
    public function index(
        Request $req, 
        CryptoRepository $cryptos, 
        TransactionRepository $txRepo,
        AssetRepository $assetRepo,
        SavingsAccountRepository $savingsRepo,
        WithdrawalRepository $withdrawalRepo
    ): Response
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

        // Get new entities data
        $assets = $assetRepo->findBy(['user' => $user], ['createdAt' => 'DESC'], 5);
        $savingsAccounts = $savingsRepo->findBy(['user' => $user], ['createdAt' => 'DESC'], 5);
        $recentWithdrawals = $withdrawalRepo->findBy(['user' => $user], ['date' => 'DESC'], 5);

        // Calculate totals
        $totalAssets = $assetRepo->count(['user' => $user]);
        $totalSavingsAccounts = $savingsRepo->count(['user' => $user]);
        $totalWithdrawals = $withdrawalRepo->count(['user' => $user]);
        
        // Calculate total savings balance
        $totalSavingsBalance = $savingsRepo->getTotalBalanceByUser($user);
        
        // Calculate total withdrawal amount
        $totalWithdrawalAmount = $withdrawalRepo->getTotalAmountByUser($user);

        return $this->render('dashboard/index.html.twig', [
            'portfolio_data' => $portfolioData,
            'stats' => $stats,
            'recent_transactions' => $recentTransactions,
            'assets' => $assets,
            'savings_accounts' => $savingsAccounts,
            'recent_withdrawals' => $recentWithdrawals,
            'total_assets' => $totalAssets,
            'total_savings_accounts' => $totalSavingsAccounts,
            'total_withdrawals' => $totalWithdrawals,
            'total_savings_balance' => $totalSavingsBalance,
            'total_withdrawal_amount' => $totalWithdrawalAmount,
        ]);
    }
}
