<?php

namespace App\Controller;

use App\Service\PortfolioCalculatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/analytics')]
class AnalyticsController extends AbstractController
{
    public function __construct(
        private PortfolioCalculatorService $portfolioCalculator
    ) {}

    #[Route('/', name: 'app_analytics_index', methods: ['GET'])]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $portfolioData = $this->portfolioCalculator->calculatePortfolio($user);
        $stats = $this->portfolioCalculator->getPortfolioStats($portfolioData);

        return $this->render('analytics/index.html.twig', [
            'portfolio_data' => $portfolioData,
            'stats' => $stats
        ]);
    }

    #[Route('/performance', name: 'app_analytics_performance', methods: ['GET'])]
    public function performance(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $portfolioData = $this->portfolioCalculator->calculatePortfolio($user);
        
        // Generate performance data for charts
        $performanceData = $this->generatePerformanceData($portfolioData);

        return $this->json($performanceData);
    }
    
    private function generatePerformanceData(array $portfolioData): array
    {
        // Generate sample performance data for the last 30 days
        $labels = [];
        $values = [];
        $invested = [];
        
        $baseValue = $portfolioData['portfolio_value']['total_value'] * 0.8; // Start 20% lower
        $baseInvested = $portfolioData['portfolio_value']['total_invested'];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = new \DateTime();
            $date->modify("-{$i} days");
            $labels[] = $date->format('d/m');
            
            // Simulate realistic portfolio growth with some volatility
            $variation = (rand(-5, 8) / 100) + ($i * 0.01); // Gradual upward trend
            $values[] = $baseValue * (1 + $variation);
            
            // Invested amount grows gradually (DCA simulation)
            $invested[] = $baseInvested * (1 + ($i * 0.005));
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'invested' => $invested
        ];
    }
}
