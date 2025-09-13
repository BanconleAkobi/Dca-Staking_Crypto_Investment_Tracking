<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class StripeService
{
    private StripeClient $stripe;
    private EntityManagerInterface $entityManager;
    private ParameterBagInterface $parameterBag;

    public function __construct(EntityManagerInterface $entityManager, string $stripeSecretKey, ParameterBagInterface $parameterBag)
    {
        $this->stripe = new StripeClient($stripeSecretKey);
        $this->entityManager = $entityManager;
        $this->parameterBag = $parameterBag;
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


    public function getCheckoutSession(string $sessionId)
    {
        try {
            return $this->stripe->checkout->sessions->retrieve($sessionId);
        } catch (ApiErrorException $e) {
            throw new \Exception('Erreur lors de la récupération de la session: ' . $e->getMessage());
        }
    }

    public function updateUserSubscription(User $user, string $plan, string $stripeCustomerId): void
    {
        $subscription = $user->getSubscription();
        
        if (!$subscription) {
            $subscription = new UserSubscription();
            $subscription->setUser($user);
            $this->entityManager->persist($subscription);
        }

        $subscription->setPlan($plan);
        $subscription->setStripeCustomerId($stripeCustomerId);
        $subscription->setStatus('active');
        $subscription->setCurrentPeriodStart(new \DateTimeImmutable());
        $subscription->setCurrentPeriodEnd((new \DateTimeImmutable())->modify('+1 month'));

        $this->entityManager->flush();
    }

    public function updateUserSubscriptionWithStripeId(User $user, string $plan, string $stripeCustomerId, string $stripeSubscriptionId): void
    {
        $subscription = $user->getSubscription();
        
        if (!$subscription) {
            $subscription = new UserSubscription();
            $subscription->setUser($user);
            $this->entityManager->persist($subscription);
        }

        $subscription->setPlan($plan);
        $subscription->setStripeCustomerId($stripeCustomerId);
        $subscription->setStripeSubscriptionId($stripeSubscriptionId);
        $subscription->setStatus('active');
        $subscription->setCurrentPeriodStart(new \DateTimeImmutable());
        $subscription->setCurrentPeriodEnd((new \DateTimeImmutable())->modify('+1 month'));

        $this->entityManager->flush();
    }

    public function getInvoiceFromSession(string $sessionId): ?array
    {
        try {
            $session = $this->stripe->checkout->sessions->retrieve($sessionId);
            
            if ($session->invoice) {
                $invoice = $this->stripe->invoices->retrieve($session->invoice);
                
                return [
                    'id' => $invoice->id,
                    'amount' => $invoice->amount_paid / 100, // Convertir de centimes
                    'currency' => $invoice->currency,
                    'status' => $invoice->status,
                    'created' => $invoice->created,
                    'invoice_pdf' => $invoice->invoice_pdf,
                    'hosted_invoice_url' => $invoice->hosted_invoice_url,
                ];
            }
            
            return null;
        } catch (ApiErrorException $e) {
            throw new \Exception('Erreur lors de la récupération de la facture: ' . $e->getMessage());
        }
    }

    public function findCustomerByEmail(string $email)
    {
        try {
            $customers = $this->stripe->customers->all(['email' => $email, 'limit' => 1]);
            return $customers->data[0] ?? null;
        } catch (ApiErrorException $e) {
            return null;
        }
    }

    public function getRecentSubscriptions(string $customerId): array
    {
        try {
            $subscriptions = $this->stripe->subscriptions->all([
                'customer' => $customerId,
                'status' => 'active',
                'limit' => 1
            ]);
            
            $result = [];
            foreach ($subscriptions->data as $subscription) {
                $result[] = [
                    'id' => $subscription->id,
                    'amount' => $subscription->items->data[0]->price->unit_amount / 100,
                    'status' => $subscription->status,
                    'created' => $subscription->created,
                ];
            }
            
            return $result;
        } catch (ApiErrorException $e) {
            throw new \Exception('Erreur lors de la récupération des abonnements: ' . $e->getMessage());
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
                    'Jusqu\'à 5 actifs',
                    'Jusqu\'à 3 comptes d\'épargne',
                    'Jusqu\'à 10 transactions',
                    'Jusqu\'à 10 retraits',
                    'Suivi de portefeuille basique',
                    'Analyses simples',
                    'Support par email',
                ],
                'limits' => [
                    'max_cryptos' => 3,
                    'max_assets' => 5,
                    'max_savings_accounts' => 3,
                    'max_withdrawals' => 10,
                    'max_transactions' => 10,
                    'pdf_reports' => false,
                ],
            ],
            'pro' => [
                'name' => 'Pro',
                'price' => 9.99,
                'currency' => 'eur',
                'interval' => 'month',
                'price_id' => 'price_1S5uoBDWwd7rIwOP9h23pFRI', // Remplacez par votre vrai price_id Stripe
                'payment_link' => $this->parameterBag->get('stripe.payment_link.pro'),
                'features' => [
                    'Jusqu\'à 50 cryptomonnaies',
                    'Jusqu\'à 100 actifs',
                    'Jusqu\'à 20 comptes d\'épargne',
                    'Jusqu\'à 1000 transactions',
                    'Jusqu\'à 500 retraits',
                    'Rapports PDF détaillés',
                    'Analyses avancées',
                    'Export de données',
                    'Support prioritaire',
                ],
                'limits' => [
                    'max_cryptos' => 50,
                    'max_assets' => 100,
                    'max_savings_accounts' => 20,
                    'max_withdrawals' => 500,
                    'max_transactions' => 1000,
                    'pdf_reports' => true,
                ],
            ],
            'enterprise' => [
                'name' => 'Entreprise',
                'price' => 29.99,
                'currency' => 'eur',
                'interval' => 'month',
                'price_id' => 'price_1S5utwDWwd7rIwOPVytRE00d', // Remplacez par votre vrai price_id Stripe
                'payment_link' => $this->parameterBag->get('stripe.payment_link.enterprise'),
                'features' => [
                    'Cryptomonnaies illimitées',
                    'Actifs illimités',
                    'Comptes d\'épargne illimités',
                    'Transactions illimitées',
                    'Retraits illimités',
                    'Rapports PDF avancés',
                    'Analyses personnalisées',
                    'Export de données complet',
                    'Support dédié',
                    'Intégrations personnalisées',
                ],
                'limits' => [
                    'max_cryptos' => -1,
                    'max_assets' => -1,
                    'max_savings_accounts' => -1,
                    'max_withdrawals' => -1,
                    'max_transactions' => -1,
                    'pdf_reports' => true,
                ],
            ],
        ];
    }

}
