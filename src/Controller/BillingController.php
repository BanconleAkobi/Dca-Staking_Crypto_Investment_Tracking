<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSubscription;
use App\Service\StripeService;
use App\Service\InvoiceEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/billing', name: 'app_billing_')]
#[IsGranted('ROLE_USER')]
class BillingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StripeService $stripeService,
        private InvoiceEmailService $invoiceEmailService
    ) {}

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $user = $this->getUser();
        $plans = $this->stripeService->getPlans();
        $currentPlan = $user->getSubscriptionPlan();
        $subscription = $user->getSubscription();

        return $this->render('billing/index.html.twig', [
            'plans' => $plans,
            'current_plan' => $currentPlan,
            'subscription' => $subscription,
        ]);
    }

    #[Route('/subscribe/{plan}', name: 'subscribe')]
    public function subscribe(string $plan): Response
    {
        $user = $this->getUser();
        $plans = $this->stripeService->getPlans();

        if (!isset($plans[$plan]) || $plan === 'free') {
            $this->addFlash('error', 'Plan invalide.');
            return $this->redirectToRoute('app_billing_index');
        }

        if ($user->getSubscriptionPlan() === $plan) {
            $this->addFlash('info', 'Vous êtes déjà abonné à ce plan.');
            return $this->redirectToRoute('app_billing_index');
        }

        // Utilisation des liens de paiement Stripe
        $paymentLink = $plans[$plan]['payment_link'] ?? null;
        if (!$paymentLink) {
            $this->addFlash('error', 'Lien de paiement non configuré pour ce plan.');
            return $this->redirectToRoute('app_billing_index');
        }

        // Redirection directe vers le lien de paiement Stripe
        return $this->redirect($paymentLink);
    }

    #[Route('/success', name: 'billing_success')]
    public function success(Request $request): Response
    {
        $user = $this->getUser();
        $sessionId = $request->query->get('session_id');
        
        // Si pas de session_id, on essaie de récupérer les infos du customer Stripe
        if (!$sessionId) {
            // Pour les liens de paiement, on va chercher les abonnements récents du customer
            try {
                $customerId = $this->findOrCreateStripeCustomer($user);
                $subscriptions = $this->stripeService->getRecentSubscriptions($customerId);
                
                if (!empty($subscriptions)) {
                    $latestSubscription = $subscriptions[0];
                    $plan = $this->determinePlanFromAmount($latestSubscription['amount']);
                    
                    if ($plan) {
                        $this->stripeService->updateUserSubscription($user, $plan, $customerId);
                        $this->addFlash('success', 'Votre abonnement ' . ucfirst($plan) . ' a été activé avec succès !');
                    }
                } else {
                    $this->addFlash('warning', 'Paiement détecté mais abonnement non trouvé. Contactez le support.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la vérification du paiement: ' . $e->getMessage());
            }
            
            return $this->redirectToRoute('app_dashboard');
        }

        // Logique originale pour les sessions avec session_id
        try {
            $session = $this->stripeService->getCheckoutSession($sessionId);
            
            if ($session && $session->payment_status === 'paid') {
                $amount = $session->amount_total / 100;
                $plan = $this->determinePlanFromAmount($amount);
                
                if ($plan) {
                    $this->stripeService->updateUserSubscription($user, $plan, $session->customer);
                    
                    $invoice = $this->stripeService->getInvoiceFromSession($sessionId);
                    if ($invoice) {
                        $this->invoiceEmailService->sendInvoiceEmail($user, $invoice, $plan);
                    }
                    
                    $this->invoiceEmailService->sendWelcomeEmail($user, $plan);
                    $this->addFlash('success', 'Votre abonnement ' . ucfirst($plan) . ' a été activé avec succès ! Un email de confirmation vous a été envoyé.');
                }
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la vérification du paiement: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_dashboard');
    }

    private function findOrCreateStripeCustomer(User $user): string
    {
        // Chercher d'abord si le customer existe déjà
        $existingCustomer = $this->stripeService->findCustomerByEmail($user->getEmail());
        if ($existingCustomer) {
            return $existingCustomer->id;
        }
        
        // Créer un nouveau customer
        return $this->stripeService->createCustomer($user);
    }

    private function determinePlanFromAmount(float $amount): ?string
    {
        // Déterminer le plan basé sur le montant payé
        if ($amount >= 29.99) {
            return 'enterprise';
        } elseif ($amount >= 9.99) {
            return 'pro';
        }
        
        return null;
    }

    #[Route('/cancel', name: 'billing_cancel')]
    public function cancel(): Response
    {
        $this->addFlash('info', 'Paiement annulé. Vous pouvez réessayer à tout moment.');
        return $this->redirectToRoute('app_billing_index');
    }


    #[Route('/portal', name: 'portal')]
    public function portal(): Response
    {
        $user = $this->getUser();
        $subscription = $user->getSubscription();

        if (!$subscription || !$subscription->getStripeCustomerId()) {
            $this->addFlash('error', 'Aucun abonnement trouvé.');
            return $this->redirectToRoute('app_billing_index');
        }

        try {
            $returnUrl = $this->generateUrl('app_billing_index');
            $portalUrl = $this->stripeService->createBillingPortalSession($user, $returnUrl);
            
            return $this->redirect($portalUrl);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'accès au portail de facturation: ' . $e->getMessage());
            return $this->redirectToRoute('app_billing_index');
        }
    }


}
