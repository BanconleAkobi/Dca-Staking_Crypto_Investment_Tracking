<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Crypto;
use App\Entity\Transaction;

class SubscriptionAccessService
{
    public function canAddCrypto(User $user): bool
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);
        $currentCount = $user->getCryptos()->count();

        if ($limits['max_cryptos'] === -1) {
            return true; // Unlimited
        }

        return $currentCount < $limits['max_cryptos'];
    }

    public function canAddAsset(User $user): bool
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);
        $currentCount = $user->getAssets()->count();

        if ($limits['max_assets'] === -1) {
            return true; // Unlimited
        }

        return $currentCount < $limits['max_assets'];
    }

    public function canAddSavingsAccount(User $user): bool
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);
        $currentCount = $user->getSavingsAccounts()->count();

        if ($limits['max_savings_accounts'] === -1) {
            return true; // Unlimited
        }

        return $currentCount < $limits['max_savings_accounts'];
    }

    public function canAddWithdrawal(User $user): bool
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);
        $currentCount = $user->getWithdrawals()->count();

        if ($limits['max_withdrawals'] === -1) {
            return true; // Unlimited
        }

        return $currentCount < $limits['max_withdrawals'];
    }

    public function canAddTransaction(User $user): bool
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);
        $currentCount = $user->getTransactions()->count();

        if ($limits['max_transactions'] === -1) {
            return true; // Unlimited
        }

        return $currentCount < $limits['max_transactions'];
    }

    public function canGeneratePdfReport(User $user): bool
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);

        return $limits['pdf_reports'] === true;
    }

    public function canAccessAdvancedAnalytics(User $user): bool
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);

        return isset($limits['advanced_analytics']) && $limits['advanced_analytics'] === true;
    }

    public function canExportData(User $user): bool
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);

        return isset($limits['export_data']) && $limits['export_data'] === true;
    }

    public function getUpgradeMessage(string $feature): string
    {
        return match($feature) {
            'crypto' => 'Vous avez atteint la limite de cryptomonnaies pour votre plan gratuit. Passez au plan Pro pour ajouter jusqu\'à 50 cryptomonnaies.',
            'asset' => 'Vous avez atteint la limite d\'actifs pour votre plan gratuit. Passez au plan Pro pour ajouter jusqu\'à 100 actifs.',
            'savings_account' => 'Vous avez atteint la limite de comptes d\'épargne pour votre plan gratuit. Passez au plan Pro pour ajouter jusqu\'à 20 comptes.',
            'withdrawal' => 'Vous avez atteint la limite de retraits pour votre plan gratuit. Passez au plan Pro pour ajouter jusqu\'à 500 retraits.',
            'transaction' => 'Vous avez atteint la limite de transactions pour votre plan gratuit. Passez au plan Pro pour ajouter jusqu\'à 1000 transactions.',
            'pdf' => 'Les rapports PDF sont disponibles avec les plans Pro et Entreprise. Passez à un plan payant pour générer des rapports détaillés.',
            'analytics' => 'Les analyses avancées sont disponibles avec les plans Pro et Entreprise. Passez à un plan payant pour accéder à plus d\'insights.',
            'export' => 'L\'export de données est disponible avec les plans Pro et Entreprise. Passez à un plan payant pour exporter vos données.',
            default => 'Cette fonctionnalité nécessite un plan payant. Passez à un plan Pro ou Entreprise pour y accéder.'
        };
    }

    public function getPlanLimits(string $plan): array
    {
        return match($plan) {
            'free' => [
                'max_cryptos' => 3,
                'max_assets' => 5,
                'max_savings_accounts' => 3,
                'max_withdrawals' => 10,
                'max_transactions' => 10,
                'pdf_reports' => false,
                'api_calls_per_day' => 100,
                'portfolio_tracking' => true,
                'basic_analytics' => true,
            ],
            'pro' => [
                'max_cryptos' => 50,
                'max_assets' => 100,
                'max_savings_accounts' => 20,
                'max_withdrawals' => 500,
                'max_transactions' => 1000,
                'pdf_reports' => true,
                'api_calls_per_day' => 1000,
                'portfolio_tracking' => true,
                'advanced_analytics' => true,
                'export_data' => true,
            ],
            'enterprise' => [
                'max_cryptos' => -1, // unlimited
                'max_assets' => -1, // unlimited
                'max_savings_accounts' => -1, // unlimited
                'max_withdrawals' => -1, // unlimited
                'max_transactions' => -1, // unlimited
                'pdf_reports' => true,
                'api_calls_per_day' => 10000,
                'portfolio_tracking' => true,
                'advanced_analytics' => true,
                'export_data' => true,
                'priority_support' => true,
                'custom_integrations' => true,
            ],
            default => [
                'max_cryptos' => 3,
                'max_assets' => 5,
                'max_savings_accounts' => 3,
                'max_withdrawals' => 10,
                'max_transactions' => 10,
                'pdf_reports' => false,
                'api_calls_per_day' => 100,
                'portfolio_tracking' => true,
                'basic_analytics' => true,
            ]
        };
    }

    public function getRemainingUsage(User $user, string $type): int
    {
        $plan = $user->getSubscriptionPlan();
        $limits = $this->getPlanLimits($plan);

        $currentCount = match($type) {
            'crypto' => $user->getCryptos()->count(),
            'asset' => $user->getAssets()->count(),
            'savings_account' => $user->getSavingsAccounts()->count(),
            'withdrawal' => $user->getWithdrawals()->count(),
            'transaction' => $user->getTransactions()->count(),
            default => 0
        };

        $limit = $limits['max_' . $type] ?? 0;

        if ($limit === -1) {
            return -1; // Unlimited
        }

        return max(0, $limit - $currentCount);
    }
}
