<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PortfolioCalculatorService;
use App\Service\InvestmentAnalyticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/investment', name: 'app_investment_')]
#[IsGranted('ROLE_USER')]
class InvestmentController extends AbstractController
{
    public function __construct(
        private PortfolioCalculatorService $portfolioCalculator,
        private InvestmentAnalyticsService $investmentAnalytics
    ) {}

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        
        // Calculs du portefeuille
        $portfolioData = $this->portfolioCalculator->calculatePortfolio($user);
        
        // Analyses avancées
        $analytics = $this->investmentAnalytics->getPortfolioAnalytics($user);
        
        // Données pour le tableau de bord
        $portfolioValue = $portfolioData['portfolio_value'] ?? [];
        $totalInvested = $portfolioValue['total_invested'] ?? 0;
        $totalValue = $portfolioValue['total_value'] ?? 0;
        
        $dashboardData = [
            'total_invested' => $totalInvested,
            'total_value' => $totalValue,
            'total_gain' => $totalValue - $totalInvested,
            'total_gain_percent' => $totalInvested > 0 ? 
                (($totalValue - $totalInvested) / $totalInvested) * 100 : 0,
            'active_investments' => $analytics['active_investments'] ?? 0,
            'completed_investments' => $analytics['completed_investments'] ?? 0,
            'upcoming_payments' => $analytics['upcoming_payments'] ?? [],
            'sector_distribution' => $analytics['sector_distribution'] ?? [],
            'asset_type_distribution' => $analytics['asset_type_distribution'] ?? [],
            'health_status' => $analytics['health_status'] ?? [],
            'monthly_evolution' => $analytics['monthly_evolution'] ?? [],
        ];

        return $this->render('investment/dashboard.html.twig', [
            'dashboard_data' => $dashboardData,
            'user' => $user,
        ]);
    }

    #[Route('/investments', name: 'investments')]
    public function investments(): Response
    {
        $user = $this->getUser();
        
        // Récupérer tous les investissements avec détails
        $investments = $this->investmentAnalytics->getAllInvestments($user);
        
        return $this->render('investment/investments.html.twig', [
            'investments' => $investments,
            'user' => $user,
        ]);
    }

    #[Route('/investment/{id}', name: 'investment_detail')]
    public function investmentDetail(int $id): Response
    {
        $user = $this->getUser();
        
        // Récupérer les détails d'un investissement spécifique
        $investment = $this->investmentAnalytics->getInvestmentDetail($user, $id);
        
        if (!$investment) {
            throw $this->createNotFoundException('Investissement non trouvé');
        }

        return $this->render('investment/detail.html.twig', [
            'investment' => $investment,
            'user' => $user,
        ]);
    }

    #[Route('/calendar', name: 'calendar')]
    public function calendar(): Response
    {
        $user = $this->getUser();
        
        // Récupérer le calendrier des échéances
        $calendar = $this->investmentAnalytics->getPaymentCalendar($user);
        
        return $this->render('investment/calendar.html.twig', [
            'calendar' => $calendar,
            'user' => $user,
        ]);
    }

    #[Route('/assemblies', name: 'assemblies')]
    public function assemblies(): Response
    {
        $user = $this->getUser();
        
        // Récupérer les assemblées générales
        $assemblies = $this->investmentAnalytics->getAssemblies($user);
        
        return $this->render('investment/assemblies.html.twig', [
            'assemblies' => $assemblies,
            'user' => $user,
        ]);
    }

    #[Route('/documents', name: 'documents')]
    public function documents(): Response
    {
        $user = $this->getUser();
        
        // Récupérer tous les documents
        $documents = $this->investmentAnalytics->getAllDocuments($user);
        
        return $this->render('investment/documents.html.twig', [
            'documents' => $documents,
            'user' => $user,
        ]);
    }
}
