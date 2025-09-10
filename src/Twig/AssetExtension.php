<?php

namespace App\Twig;

use App\Entity\Asset;
use App\Entity\SavingsAccount;
use App\Entity\Withdrawal;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_asset_type_icon', [$this, 'getAssetTypeIcon']),
            new TwigFunction('get_asset_type_label', [$this, 'getAssetTypeLabel']),
            new TwigFunction('get_savings_account_type_icon', [$this, 'getSavingsAccountTypeIcon']),
            new TwigFunction('get_savings_account_type_label', [$this, 'getSavingsAccountTypeLabel']),
            new TwigFunction('get_withdrawal_type_icon', [$this, 'getWithdrawalTypeIcon']),
            new TwigFunction('get_withdrawal_type_label', [$this, 'getWithdrawalTypeLabel']),
            new TwigFunction('get_withdrawal_status_icon', [$this, 'getWithdrawalStatusIcon']),
            new TwigFunction('get_withdrawal_status_color', [$this, 'getWithdrawalStatusColor']),
        ];
    }

    public function getAssetTypeIcon(string $type): string
    {
        return match($type) {
            Asset::TYPE_CRYPTO => 'coins',
            Asset::TYPE_STOCK => 'chart-line',
            Asset::TYPE_ETF => 'chart-area',
            Asset::TYPE_BOND => 'hand-holding-usd',
            Asset::TYPE_SAVINGS => 'piggy-bank',
            Asset::TYPE_REAL_ESTATE => 'home',
            Asset::TYPE_COMMODITY => 'box',
            Asset::TYPE_CURRENCY => 'dollar-sign',
            default => 'chart-pie'
        };
    }

    public function getAssetTypeLabel(string $type): string
    {
        return match($type) {
            Asset::TYPE_CRYPTO => 'Cryptomonnaies',
            Asset::TYPE_STOCK => 'Actions',
            Asset::TYPE_ETF => 'ETF',
            Asset::TYPE_BOND => 'Obligations',
            Asset::TYPE_SAVINGS => 'Épargne',
            Asset::TYPE_REAL_ESTATE => 'Immobilier',
            Asset::TYPE_COMMODITY => 'Matières premières',
            Asset::TYPE_CURRENCY => 'Devises',
            default => 'Actifs'
        };
    }

    public function getSavingsAccountTypeIcon(string $type): string
    {
        return match($type) {
            SavingsAccount::TYPE_LIVRET_A => 'piggy-bank',
            SavingsAccount::TYPE_LDDS => 'leaf',
            SavingsAccount::TYPE_LEP => 'heart',
            SavingsAccount::TYPE_PEL => 'home',
            SavingsAccount::TYPE_CEL => 'home',
            SavingsAccount::TYPE_LAJ => 'child',
            SavingsAccount::TYPE_LDD => 'leaf',
            SavingsAccount::TYPE_LEP_PLUS => 'heart',
            SavingsAccount::TYPE_TERM_DEPOSIT => 'calendar-alt',
            SavingsAccount::TYPE_SAVINGS_BOND => 'certificate',
            SavingsAccount::TYPE_ASSURANCE_VIE => 'shield-alt',
            SavingsAccount::TYPE_PEA => 'chart-line',
            SavingsAccount::TYPE_PEA_PME => 'building',
            default => 'piggy-bank'
        };
    }

    public function getSavingsAccountTypeLabel(string $type): string
    {
        return match($type) {
            SavingsAccount::TYPE_LIVRET_A => 'Livret A',
            SavingsAccount::TYPE_LDDS => 'LDDS',
            SavingsAccount::TYPE_LEP => 'LEP',
            SavingsAccount::TYPE_PEL => 'PEL',
            SavingsAccount::TYPE_CEL => 'CEL',
            SavingsAccount::TYPE_LAJ => 'Livret A Jeune',
            SavingsAccount::TYPE_LDD => 'LDD',
            SavingsAccount::TYPE_LEP_PLUS => 'LEP+',
            SavingsAccount::TYPE_TERM_DEPOSIT => 'Dépôt à terme',
            SavingsAccount::TYPE_SAVINGS_BOND => 'Obligation d\'épargne',
            SavingsAccount::TYPE_ASSURANCE_VIE => 'Assurance vie',
            SavingsAccount::TYPE_PEA => 'PEA',
            SavingsAccount::TYPE_PEA_PME => 'PEA-PME',
            default => 'Compte d\'épargne'
        };
    }

    public function getWithdrawalTypeIcon(string $type): string
    {
        return match($type) {
            Withdrawal::TYPE_RETIREMENT => 'umbrella',
            Withdrawal::TYPE_EMERGENCY => 'exclamation-triangle',
            Withdrawal::TYPE_REBALANCING => 'balance-scale',
            Withdrawal::TYPE_TAX_PAYMENT => 'receipt',
            Withdrawal::TYPE_PURCHASE => 'shopping-cart',
            Withdrawal::TYPE_INVESTMENT => 'chart-line',
            Withdrawal::TYPE_OTHER => 'ellipsis-h',
            default => 'money-bill-wave'
        };
    }

    public function getWithdrawalTypeLabel(string $type): string
    {
        return match($type) {
            Withdrawal::TYPE_RETIREMENT => 'Retraite',
            Withdrawal::TYPE_EMERGENCY => 'Urgence',
            Withdrawal::TYPE_REBALANCING => 'Rééquilibrage',
            Withdrawal::TYPE_TAX_PAYMENT => 'Paiement d\'impôts',
            Withdrawal::TYPE_PURCHASE => 'Achat',
            Withdrawal::TYPE_INVESTMENT => 'Réinvestissement',
            Withdrawal::TYPE_OTHER => 'Autre',
            default => 'Retrait'
        };
    }

    public function getWithdrawalStatusIcon(string $status): string
    {
        return match($status) {
            Withdrawal::STATUS_PENDING => 'clock',
            Withdrawal::STATUS_COMPLETED => 'check-circle',
            Withdrawal::STATUS_CANCELLED => 'times-circle',
            default => 'question-circle'
        };
    }

    public function getWithdrawalStatusColor(string $status): string
    {
        return match($status) {
            Withdrawal::STATUS_PENDING => 'warning',
            Withdrawal::STATUS_COMPLETED => 'success',
            Withdrawal::STATUS_CANCELLED => 'danger',
            default => 'secondary'
        };
    }
}
