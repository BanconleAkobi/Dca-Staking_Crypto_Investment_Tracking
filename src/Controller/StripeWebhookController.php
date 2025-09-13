<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/webhook', name: 'app_webhook_')]
class StripeWebhookController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StripeService $stripeService
    ) {}

    #[Route('/stripe', name: 'stripe', methods: ['POST'])]
    public function stripe(Request $request): Response
    {
        $payload = $request->getContent();
        $sigHeader = $request->headers->get('stripe-signature');
        $endpointSecret = $_ENV['STRIPE_WEBHOOK_SECRET'] ?? null;

        if (!$endpointSecret) {
            return new Response('Webhook secret not configured', 400);
        }

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;
            case 'customer.subscription.created':
                $this->handleSubscriptionCreated($event->data->object);
                break;
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;
            default:
                // Unhandled event type
                break;
        }

        return new Response('OK', 200);
    }

    private function handleCheckoutSessionCompleted($session): void
    {
        // Handle successful checkout session
        if ($session->mode === 'subscription' && $session->payment_status === 'paid') {
            $customerId = $session->customer;
            $subscriptionId = $session->subscription;
            
            // Find user by customer ID
            $user = $this->findUserByStripeCustomerId($customerId);
            if ($user) {
                // Get subscription details from Stripe
                $stripeSubscription = $this->stripeService->getSubscription($subscriptionId);
                if ($stripeSubscription) {
                    $amount = $stripeSubscription->items->data[0]->price->unit_amount / 100;
                    $plan = $this->determinePlanFromAmount($amount);
                    
                    if ($plan) {
                        $this->stripeService->updateUserSubscriptionWithStripeId(
                            $user,
                            $plan,
                            $customerId,
                            $subscriptionId
                        );
                    }
                }
            }
        }
    }

    private function handleSubscriptionCreated($subscription): void
    {
        $customerId = $subscription->customer;
        $user = $this->findUserByStripeCustomerId($customerId);
        
        if ($user && $subscription->status === 'active') {
            $amount = $subscription->items->data[0]->price->unit_amount / 100;
            $plan = $this->determinePlanFromAmount($amount);
            
            if ($plan) {
                $this->stripeService->updateUserSubscriptionWithStripeId(
                    $user,
                    $plan,
                    $customerId,
                    $subscription->id
                );
            }
        }
    }

    private function handleSubscriptionUpdated($subscription): void
    {
        $customerId = $subscription->customer;
        $user = $this->findUserByStripeCustomerId($customerId);
        
        if ($user) {
            $userSubscription = $user->getSubscription();
            if ($userSubscription) {
                $userSubscription->setStatus($subscription->status);
                
                if ($subscription->status === 'canceled') {
                    $userSubscription->setCanceledAt(new \DateTimeImmutable());
                }
                
                $this->entityManager->flush();
            }
        }
    }

    private function handleSubscriptionDeleted($subscription): void
    {
        $customerId = $subscription->customer;
        $user = $this->findUserByStripeCustomerId($customerId);
        
        if ($user) {
            $userSubscription = $user->getSubscription();
            if ($userSubscription) {
                $userSubscription->setStatus('canceled');
                $userSubscription->setCanceledAt(new \DateTimeImmutable());
                $this->entityManager->flush();
            }
        }
    }

    private function handleInvoicePaymentSucceeded($invoice): void
    {
        // Handle successful payment
        $customerId = $invoice->customer;
        $user = $this->findUserByStripeCustomerId($customerId);
        
        if ($user) {
            $userSubscription = $user->getSubscription();
            if ($userSubscription) {
                $userSubscription->setStatus('active');
                $this->entityManager->flush();
            }
        }
    }

    private function handleInvoicePaymentFailed($invoice): void
    {
        // Handle failed payment
        $customerId = $invoice->customer;
        $user = $this->findUserByStripeCustomerId($customerId);
        
        if ($user) {
            $userSubscription = $user->getSubscription();
            if ($userSubscription) {
                $userSubscription->setStatus('past_due');
                $this->entityManager->flush();
            }
        }
    }

    private function findUserByStripeCustomerId(string $customerId): ?User
    {
        return $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->join('u.subscription', 's')
            ->where('s.stripeCustomerId = :customerId')
            ->setParameter('customerId', $customerId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function determinePlanFromAmount(float $amount): ?string
    {
        if ($amount >= 29.99) {
            return 'enterprise';
        } elseif ($amount >= 9.99) {
            return 'pro';
        }
        
        return null;
    }
}
