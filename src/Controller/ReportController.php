<?php

namespace App\Controller;

use App\Service\PdfReportService;
use App\Service\SubscriptionAccessService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reports', name: 'app_reports_')]
#[IsGranted('ROLE_USER')]
class ReportController extends AbstractController
{
    public function __construct(
        private PdfReportService $pdfReportService,
        private SubscriptionAccessService $subscriptionAccessService
    ) {}

    #[Route('/portfolio', name: 'portfolio')]
    public function portfolioReport(): Response
    {
        $user = $this->getUser();

        if (!$this->subscriptionAccessService->canGeneratePdfReport($user)) {
            $this->addFlash('error', $this->subscriptionAccessService->getUpgradeMessage('pdf'));
            return $this->redirectToRoute('app_billing_index');
        }

        try {
            $pdfContent = $this->pdfReportService->generatePortfolioReport($user);
            
            $response = new Response($pdfContent);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 
                $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                    'rapport-portefeuille-' . date('Y-m-d') . '.pdf'
                )
            );

            return $response;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la génération du rapport: ' . $e->getMessage());
            return $this->redirectToRoute('app_dashboard');
        }
    }
}
