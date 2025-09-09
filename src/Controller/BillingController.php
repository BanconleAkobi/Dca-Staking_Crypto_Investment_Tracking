<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSubscription;
use App\Service\StripeService;
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
        private StripeService $stripeService
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

        try {
            $priceId = $plans[$plan]['stripe_price_id'] ?? null;
            if (!$priceId) {
                throw new \Exception('Price ID non configuré pour ce plan.');
            }

            $successUrl = $this->generateUrl('app_billing_success', ['session_id' => '{CHECKOUT_SESSION_ID}']);
            $cancelUrl = $this->generateUrl('app_billing_cancel');

            $sessionId = $this->stripeService->createCheckoutSession(
                $user,
                $priceId,
                $successUrl,
                $cancelUrl
            );

            return $this->redirect("https://checkout.stripe.com/pay/{$sessionId}");

        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création de la session de paiement: ' . $e->getMessage());
            return $this->redirectToRoute('app_billing_index');
        }
    }

    #[Route('/success', name: 'success')]
    public function success(Request $request): Response
    {
        $sessionId = $request->query->get('session_id');
        
        if (!$sessionId) {
            $this->addFlash('error', 'Session de paiement invalide.');
            return $this->redirectToRoute('app_billing_index');
        }

        $this->addFlash('success', 'Votre abonnement a été activé avec succès !');
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/cancel', name: 'cancel')]
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
