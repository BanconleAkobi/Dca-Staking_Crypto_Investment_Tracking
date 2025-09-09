<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Repository\CryptoRepository;

class PortfolioCalculatorService
{
    private TransactionRepository $transactionRepository;
    private CryptoRepository $cryptoRepository;
    private CryptoApiService $cryptoApiService;

    public function __construct(
        TransactionRepository $transactionRepository,
        CryptoRepository $cryptoRepository,
        CryptoApiService $cryptoApiService
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->cryptoRepository = $cryptoRepository;
        $this->cryptoApiService = $cryptoApiService;
    }

    public function calculatePortfolio(User $user): array
    {
        $transactions = $this->transactionRepository->findBy(['user' => $user]);
        $holdings = $this->calculateHoldings($transactions);
        $portfolioData = $this->cryptoApiService->getPortfolioValue($holdings);
        
        return [
            'holdings' => $holdings,
            'portfolio_value' => $portfolioData,
            'transactions_count' => count($transactions),
            'last_transaction' => $this->getLastTransaction($transactions)
        ];
    }

    public function calculateHoldings(array $transactions): array
    {
        $holdings = [];

        foreach ($transactions as $transaction) {
            $symbol = $transaction->getCrypto()->getSymbol();
            
            if (!isset($holdings[$symbol])) {
                $holdings[$symbol] = [
                    'symbol' => $symbol,
                    'name' => $transaction->getCrypto()->getName(),
                    'quantity' => 0,
                    'invested' => 0,
                    'transactions' => []
                ];
            }

            $quantity = (float) $transaction->getQuantity();
            $price = $transaction->getUnitPriceUsd() ? (float) $transaction->getUnitPriceUsd() : 0;
            $fee = $transaction->getFeeUsd() ? (float) $transaction->getFeeUsd() : 0;

            if ($transaction->getType() === 'BUY') {
                $holdings[$symbol]['quantity'] += $quantity;
                $holdings[$symbol]['invested'] += ($quantity * $price) + $fee;
            } elseif ($transaction->getType() === 'STAKE_REWARD') {
                $holdings[$symbol]['quantity'] += $quantity;
                // Staking rewards don't add to invested amount
            }

            $holdings[$symbol]['transactions'][] = [
                'id' => $transaction->getId(),
                'type' => $transaction->getType(),
                'date' => $transaction->getDate(),
                'quantity' => $quantity,
                'price' => $price,
                'fee' => $fee,
                'note' => $transaction->getNote()
            ];
        }

        // Remove holdings with zero quantity
        return array_filter($holdings, function($holding) {
            return $holding['quantity'] > 0;
        });
    }

    public function getPortfolioStats(array $portfolioData): array
    {
        $totalValue = $portfolioData['portfolio_value']['total_value'];
        $totalInvested = $portfolioData['portfolio_value']['total_invested'];
        $totalGainLoss = $portfolioData['portfolio_value']['total_gain_loss'];
        $totalGainLossPercent = $portfolioData['portfolio_value']['total_gain_loss_percent'];

        return [
            'total_value' => $totalValue,
            'total_invested' => $totalInvested,
            'total_gain_loss' => $totalGainLoss,
            'total_gain_loss_percent' => $totalGainLossPercent,
            'crypto_count' => count($portfolioData['holdings']),
            'transactions_count' => $portfolioData['transactions_count'],
            'last_transaction' => $portfolioData['last_transaction']
        ];
    }

    public function getCryptoPerformance(string $symbol, array $transactions): array
    {
        $cryptoTransactions = array_filter($transactions, function($tx) use ($symbol) {
            return $tx->getCrypto()->getSymbol() === $symbol;
        });

        $holdings = $this->calculateHoldings($cryptoTransactions);
        if (empty($holdings[$symbol])) {
            return [];
        }

        $holding = $holdings[$symbol];
        $currentPrice = $this->cryptoApiService->getCryptoPrice($symbol);
        $currentValue = $holding['quantity'] * $currentPrice;
        $gainLoss = $currentValue - $holding['invested'];
        $gainLossPercent = $holding['invested'] > 0 ? ($gainLoss / $holding['invested']) * 100 : 0;

        return [
            'symbol' => $symbol,
            'name' => $holding['name'],
            'quantity' => $holding['quantity'],
            'invested' => $holding['invested'],
            'current_price' => $currentPrice,
            'current_value' => $currentValue,
            'gain_loss' => $gainLoss,
            'gain_loss_percent' => $gainLossPercent,
            'transactions_count' => count($holding['transactions'])
        ];
    }

    private function getLastTransaction(array $transactions): ?array
    {
        if (empty($transactions)) {
            return null;
        }

        $lastTx = $transactions[0];
        foreach ($transactions as $tx) {
            if ($tx->getDate() > $lastTx->getDate()) {
                $lastTx = $tx;
            }
        }

        return [
            'id' => $lastTx->getId(),
            'date' => $lastTx->getDate(),
            'type' => $lastTx->getType(),
            'crypto' => $lastTx->getCrypto()->getSymbol(),
            'quantity' => $lastTx->getQuantity()
        ];
    }
}
