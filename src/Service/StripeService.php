<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class StripeService
{
    private StripeClient $stripe;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, string $stripeSecretKey)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
        $this->entityManager = $entityManager;
    }

    public function createCustomer(User $user): string
    {
        try {
            $customer = $this->stripe->customers->create([
                'email' => $user->getEmail(),
                'name' => $user->getPseudo() ?? $user->getEmail(),
                'metadata' => [
                    'user_id' => $user->getId(),
                ],
            ]);

            return $customer->id;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to create Stripe customer: ' . $e->getMessage());
        }
    }

    public function createCheckoutSession(User $user, string $priceId, string $successUrl, string $cancelUrl): string
    {
        try {
            $customerId = $user->getSubscription()?->getStripeCustomerId();
            
            if (!$customerId) {
                $customerId = $this->createCustomer($user);
            }

            $session = $this->stripe->checkout->sessions->create([
                'customer' => $customerId,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'metadata' => [
                    'user_id' => $user->getId(),
                ],
            ]);

            return $session->id;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to create checkout session: ' . $e->getMessage());
        }
    }

    public function createBillingPortalSession(User $user, string $returnUrl): string
    {
        try {
            $customerId = $user->getSubscription()?->getStripeCustomerId();
            
            if (!$customerId) {
                throw new \Exception('User has no Stripe customer ID');
            }

            $session = $this->stripe->billingPortal->sessions->create([
                'customer' => $customerId,
                'return_url' => $returnUrl,
            ]);

            return $session->url;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to create billing portal session: ' . $e->getMessage());
        }
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        try {
            $this->stripe->subscriptions->cancel($subscriptionId);
            return true;
        } catch (ApiErrorException $e) {
            throw new \Exception('Failed to cancel subscription: ' . $e->getMessage());
        }
    }

    public function getSubscription(string $subscriptionId): ?object
    {
        try {
            return $this->stripe->subscriptions->retrieve($subscriptionId);
        } catch (ApiErrorException $e) {
            return null;
        }
    }

    public function getCustomer(string $customerId): ?object
    {
        try {
            return $this->stripe->customers->retrieve($customerId);
        } catch (ApiErrorException $e) {
            return null;
        }
    }


    public function getPlans(): array
    {
        return [
            'free' => [
                'name' => 'Gratuit',
                'price' => 0,
                'currency' => 'eur',
                'interval' => 'month',
                'features' => [
                    'Jusqu\'à 3 cryptomonnaies',
                    'Jusqu\'à 10 transactions',
                    'Suivi de portefeuille basique',
                    'Analyses simples',
                    'Support par email',
                ],
                'limits' => [
                    'max_cryptos' => 3,
                    'max_transactions' => 10,
                    'pdf_reports' => false,
                ],
            ],
            'pro' => [
                'name' => 'Pro',
                'price' => 9.99,
                'currency' => 'eur',
                'interval' => 'month',
                'stripe_price_id' => 'price_pro_monthly', // You'll need to create this in Stripe
                'features' => [
                    'Jusqu\'à 50 cryptomonnaies',
                    'Jusqu\'à 1000 transactions',
                    'Rapports PDF détaillés',
                    'Analyses avancées',
                    'Export de données',
                    'Support prioritaire',
                ],
                'limits' => [
                    'max_cryptos' => 50,
                    'max_transactions' => 1000,
                    'pdf_reports' => true,
                ],
            ],
            'enterprise' => [
                'name' => 'Entreprise',
                'price' => 29.99,
                'currency' => 'eur',
                'interval' => 'month',
                'stripe_price_id' => 'price_enterprise_monthly', // You'll need to create this in Stripe
                'features' => [
                    'Cryptomonnaies illimitées',
                    'Transactions illimitées',
                    'Rapports PDF avancés',
                    'Analyses personnalisées',
                    'Export de données complet',
                    'Support dédié',
                    'Intégrations personnalisées',
                ],
                'limits' => [
                    'max_cryptos' => -1,
                    'max_transactions' => -1,
                    'pdf_reports' => true,
                ],
            ],
        ];
    }
}
