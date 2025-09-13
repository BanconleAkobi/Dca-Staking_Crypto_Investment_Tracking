<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Transaction;
use App\Entity\Asset;
use App\Entity\Crypto;
use App\Entity\SavingsAccount;
use Doctrine\ORM\EntityManagerInterface;

class InvestmentAnalyticsService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function getPortfolioAnalytics(User $user): array
    {
        $transactions = $user->getTransactions()->toArray();
        $assets = $user->getAssets()->toArray();
        $cryptos = $user->getCryptos()->toArray();
        $savingsAccounts = $user->getSavingsAccounts()->toArray();

        return [
            'active_investments' => $this->countActiveInvestments($transactions, $assets, $cryptos, $savingsAccounts),
            'completed_investments' => $this->countCompletedInvestments($transactions),
            'upcoming_payments' => $this->getUpcomingPayments($user),
            'sector_distribution' => $this->getSectorDistribution($assets),
            'asset_type_distribution' => $this->getAssetTypeDistribution($assets, $cryptos, $savingsAccounts),
            'health_status' => $this->getHealthStatus($assets, $cryptos),
            'monthly_evolution' => $this->getMonthlyEvolution($transactions),
        ];
    }

    public function getAllInvestments(User $user): array
    {
        $investments = [];
        
        // Cryptos
        foreach ($user->getCryptos() as $crypto) {
            $cryptoTransactions = $user->getTransactions()->filter(
                fn($t) => $t->getCrypto() && $t->getCrypto()->getId() === $crypto->getId()
            )->toArray();
            
            if (!empty($cryptoTransactions)) {
                $investments[] = [
                    'id' => 'crypto_' . $crypto->getId(),
                    'type' => 'crypto',
                    'name' => $crypto->getName(),
                    'symbol' => $crypto->getSymbol(),
                    'total_invested' => array_sum(array_map(fn($t) => $t->getQuantity() * $t->getUnitPriceUsd(), $cryptoTransactions)),
                    'current_value' => $this->calculateCurrentCryptoValue($crypto, $cryptoTransactions),
                    'status' => 'active',
                    'transactions' => $cryptoTransactions,
                    'created_at' => min(array_map(fn($t) => $t->getDate(), $cryptoTransactions)),
                ];
            }
        }

        // Assets
        foreach ($user->getAssets() as $asset) {
            $assetTransactions = $user->getTransactions()->filter(
                fn($t) => $t->getAsset() && $t->getAsset()->getId() === $asset->getId()
            )->toArray();
            
            if (!empty($assetTransactions)) {
                $investments[] = [
                    'id' => 'asset_' . $asset->getId(),
                    'type' => 'asset',
                    'name' => $asset->getName(),
                    'symbol' => $asset->getSymbol(),
                    'category' => $asset->getCategory(),
                    'total_invested' => array_sum(array_map(fn($t) => $t->getQuantity() * $t->getUnitPriceUsd(), $assetTransactions)),
                    'current_value' => $asset->getCurrentPrice() * array_sum(array_map(fn($t) => $t->getQuantity(), $assetTransactions)),
                    'status' => $asset->isActive() ? 'active' : 'inactive',
                    'transactions' => $assetTransactions,
                    'created_at' => min(array_map(fn($t) => $t->getDate(), $assetTransactions)),
                ];
            }
        }

        // Savings Accounts
        foreach ($user->getSavingsAccounts() as $account) {
            if ($account->getCurrentBalance() > 0) {
                $investments[] = [
                    'id' => 'savings_' . $account->getId(),
                    'type' => 'savings',
                    'name' => $account->getName(),
                    'bank_name' => $account->getBankName(),
                    'total_invested' => $account->getCurrentBalance(),
                    'current_value' => $account->getCurrentBalance(),
                    'annual_rate' => $account->getAnnualRate(),
                    'status' => $account->isActive() ? 'active' : 'inactive',
                    'transactions' => [],
                    'created_at' => $account->getOpeningDate(),
                ];
            }
        }

        // Trier par date de création (plus récent en premier)
        usort($investments, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $investments;
    }

    public function getInvestmentDetail(User $user, string $id): ?array
    {
        $investments = $this->getAllInvestments($user);
        
        foreach ($investments as $investment) {
            if ($investment['id'] === $id) {
                // Ajouter des détails supplémentaires
                $investment['documents'] = $this->getInvestmentDocuments($investment);
                $investment['payment_schedule'] = $this->getPaymentSchedule($investment);
                $investment['performance_history'] = $this->getPerformanceHistory($investment);
                $investment['health_indicators'] = $this->getHealthIndicators($investment);
                
                return $investment;
            }
        }

        return null;
    }

    public function getPaymentCalendar(User $user): array
    {
        $calendar = [];
        $currentDate = new \DateTime();
        
        // Générer un calendrier des 12 prochains mois
        for ($i = 0; $i < 12; $i++) {
            $month = clone $currentDate;
            $month->modify("+{$i} months");
            
            $calendar[] = [
                'month' => $month,
                'payments' => $this->getPaymentsForMonth($user, $month),
                'total_amount' => $this->getTotalPaymentsForMonth($user, $month),
            ];
        }

        return $calendar;
    }

    public function getAssemblies(User $user): array
    {
        $assemblyRepository = $this->entityManager->getRepository(\App\Entity\Assembly::class);
        $assemblies = $assemblyRepository->findUpcoming();
        
        // Si aucune assemblée, créer quelques exemples
        if (empty($assemblies)) {
            $this->createSampleAssemblies();
            $assemblies = $assemblyRepository->findUpcoming();
        }
        
        return $assemblies;
    }

    public function getAllDocuments(User $user): array
    {
        $documentRepository = $this->entityManager->getRepository(\App\Entity\Document::class);
        $documents = $documentRepository->findByUser($user);
        
        // Si aucun document, créer quelques exemples
        if (empty($documents)) {
            $this->createSampleDocuments($user);
            $documents = $documentRepository->findByUser($user);
        }
        
        return $documents;
    }

    private function countActiveInvestments(array $transactions, array $assets, array $cryptos, array $savingsAccounts): int
    {
        $count = 0;
        
        // Compter les cryptos avec transactions
        $cryptoIds = array_unique(array_map(fn($t) => $t->getCrypto()?->getId(), $transactions));
        $count += count(array_filter($cryptoIds));
        
        // Compter les assets avec transactions
        $assetIds = array_unique(array_map(fn($t) => $t->getAsset()?->getId(), $transactions));
        $count += count(array_filter($assetIds));
        
        // Compter les comptes d'épargne actifs
        $count += count(array_filter($savingsAccounts, fn($a) => $a->isActive() && $a->getCurrentBalance() > 0));
        
        return $count;
    }

    private function countCompletedInvestments(array $transactions): int
    {
        // Logique pour déterminer les investissements terminés
        // Pour l'instant, on considère qu'un investissement est terminé s'il n'y a pas eu de transaction récente
        $cutoffDate = new \DateTime('-1 year');
        $completedCount = 0;
        
        $groupedTransactions = [];
        foreach ($transactions as $transaction) {
            $key = $transaction->getCrypto()?->getId() ?? $transaction->getAsset()?->getId();
            if ($key) {
                $groupedTransactions[$key][] = $transaction;
            }
        }
        
        foreach ($groupedTransactions as $group) {
            $lastTransaction = max(array_map(fn($t) => $t->getDate(), $group));
            if ($lastTransaction < $cutoffDate) {
                $completedCount++;
            }
        }
        
        return $completedCount;
    }

    private function getUpcomingPayments(User $user): array
    {
        // Simuler des paiements à venir
        return [
            [
                'date' => new \DateTime('+15 days'),
                'amount' => 150.00,
                'investment' => 'TechCorp Inc.',
                'type' => 'dividend',
            ],
            [
                'date' => new \DateTime('+30 days'),
                'amount' => 75.50,
                'investment' => 'GreenEnergy Ltd.',
                'type' => 'interest',
            ],
        ];
    }

    private function getSectorDistribution(array $assets): array
    {
        $distribution = [];
        
        foreach ($assets as $asset) {
            $category = $asset->getCategory();
            if (!isset($distribution[$category])) {
                $distribution[$category] = 0;
            }
            $distribution[$category] += $asset->getCurrentPrice();
        }
        
        return $distribution;
    }

    private function getAssetTypeDistribution(array $assets, array $cryptos, array $savingsAccounts): array
    {
        return [
            'crypto' => count($cryptos),
            'stocks' => count(array_filter($assets, fn($a) => $a->getType() === 'stock')),
            'etf' => count(array_filter($assets, fn($a) => $a->getType() === 'etf')),
            'bonds' => count(array_filter($assets, fn($a) => $a->getType() === 'bond')),
            'savings' => count($savingsAccounts),
        ];
    }

    private function getHealthStatus(array $assets, array $cryptos): array
    {
        return [
            'excellent' => 3,
            'good' => 5,
            'fair' => 2,
            'poor' => 1,
        ];
    }

    private function getMonthlyEvolution(array $transactions): array
    {
        $evolution = [];
        $currentDate = new \DateTime();
        
        for ($i = 11; $i >= 0; $i--) {
            $month = clone $currentDate;
            $month->modify("-{$i} months");
            $month->modify('first day of this month');
            
            $monthTransactions = array_filter($transactions, 
                fn($t) => $t->getDate()->format('Y-m') === $month->format('Y-m')
            );
            
            $totalInvested = array_sum(array_map(
                fn($t) => $t->getQuantity() * $t->getUnitPriceUsd(),
                $monthTransactions
            ));
            
            $evolution[] = [
                'month' => $month->format('M Y'),
                'invested' => $totalInvested,
            ];
        }
        
        return $evolution;
    }

    private function calculateCurrentCryptoValue($crypto, array $transactions): float
    {
        // Simuler un prix actuel (dans un vrai système, utiliser une API)
        $totalQuantity = array_sum(array_map(fn($t) => $t->getQuantity(), $transactions));
        $currentPrice = 50000; // Prix simulé
        return $totalQuantity * $currentPrice;
    }

    private function getInvestmentDocuments(array $investment): array
    {
        // Simuler des documents
        return [
            [
                'name' => 'Bulletin de souscription',
                'type' => 'subscription',
                'date' => $investment['created_at'],
                'size' => '2.3 MB',
                'url' => '/documents/subscription_' . $investment['id'] . '.pdf',
            ],
            [
                'name' => 'Attestation fiscale',
                'type' => 'tax',
                'date' => new \DateTime('-30 days'),
                'size' => '1.1 MB',
                'url' => '/documents/tax_' . $investment['id'] . '.pdf',
            ],
        ];
    }

    private function getPaymentSchedule(array $investment): array
    {
        // Simuler un échéancier de paiements
        return [
            [
                'date' => new \DateTime('+30 days'),
                'amount' => 50.00,
                'type' => 'dividend',
                'status' => 'pending',
            ],
            [
                'date' => new \DateTime('+90 days'),
                'amount' => 50.00,
                'type' => 'dividend',
                'status' => 'pending',
            ],
        ];
    }

    private function getPerformanceHistory(array $investment): array
    {
        // Simuler l'historique de performance
        $history = [];
        $currentValue = $investment['current_value'];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = new \DateTime("-{$i} months");
            $value = $currentValue * (0.95 + (rand(0, 10) / 100)); // Simulation de variation
            
            $history[] = [
                'date' => $date,
                'value' => $value,
                'change' => $i === 11 ? 0 : $value - $history[count($history) - 1]['value'],
            ];
        }
        
        return $history;
    }

    private function getHealthIndicators(array $investment): array
    {
        return [
            'financial_health' => 'good',
            'market_performance' => 'excellent',
            'risk_level' => 'medium',
            'liquidity' => 'high',
        ];
    }

    private function getPaymentsForMonth(User $user, \DateTime $month): array
    {
        // Simuler des paiements pour un mois donné
        return [
            [
                'date' => clone $month,
                'amount' => 100.00,
                'investment' => 'TechCorp Inc.',
                'type' => 'dividend',
            ],
        ];
    }

    private function getTotalPaymentsForMonth(User $user, \DateTime $month): float
    {
        $payments = $this->getPaymentsForMonth($user, $month);
        return array_sum(array_map(fn($p) => $p['amount'], $payments));
    }

    private function createSampleAssemblies(): void
    {
        $assemblies = [
            [
                'company' => 'TechCorp Inc.',
                'date' => new \DateTime('+30 days'),
                'type' => 'AGO',
                'status' => 'upcoming',
                'votingOpen' => true,
                'resolutions' => [
                    'Approbation des comptes annuels',
                    'Distribution des dividendes',
                    'Renouvellement du conseil d\'administration',
                ],
                'documents' => [
                    'Convocation officielle',
                    'Rapport de gestion',
                    'Comptes annuels',
                ],
                'description' => 'Assemblée générale ordinaire annuelle',
            ],
            [
                'company' => 'GreenEnergy Ltd.',
                'date' => new \DateTime('+45 days'),
                'type' => 'AGE',
                'status' => 'upcoming',
                'votingOpen' => false,
                'resolutions' => [
                    'Augmentation de capital',
                    'Modification des statuts',
                ],
                'documents' => [
                    'Convocation officielle',
                    'Rapport spécial',
                ],
                'description' => 'Assemblée générale extraordinaire',
            ],
        ];

        foreach ($assemblies as $data) {
            $assembly = new \App\Entity\Assembly();
            $assembly->setCompany($data['company']);
            $assembly->setDate($data['date']);
            $assembly->setType($data['type']);
            $assembly->setStatus($data['status']);
            $assembly->setVotingOpen($data['votingOpen']);
            $assembly->setResolutions($data['resolutions']);
            $assembly->setDocuments($data['documents']);
            $assembly->setDescription($data['description']);

            $this->entityManager->persist($assembly);
        }

        $this->entityManager->flush();
    }

    private function createSampleDocuments(User $user): void
    {
        $documents = [
            [
                'name' => 'Bulletin de souscription - TechCorp Inc.',
                'type' => 'subscription',
                'filename' => 'subscription_techcorp.pdf',
                'filePath' => '/documents/subscription_techcorp.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 2400000,
                'documentDate' => new \DateTime('-1 month'),
                'relatedEntity' => 'asset',
                'relatedEntityId' => 1,
                'description' => 'Bulletin de souscription pour l\'investissement dans TechCorp Inc.',
            ],
            [
                'name' => 'Attestation fiscale 2024',
                'type' => 'tax',
                'filename' => 'tax_attestation_2024.pdf',
                'filePath' => '/documents/tax_attestation_2024.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 1100000,
                'documentDate' => new \DateTime('-30 days'),
                'description' => 'Attestation fiscale pour l\'année 2024',
            ],
            [
                'name' => 'Rapport semestriel - GreenEnergy Ltd.',
                'type' => 'report',
                'filename' => 'report_greenenergy_h1_2024.pdf',
                'filePath' => '/documents/report_greenenergy_h1_2024.pdf',
                'mimeType' => 'application/pdf',
                'fileSize' => 3500000,
                'documentDate' => new \DateTime('-15 days'),
                'relatedEntity' => 'asset',
                'relatedEntityId' => 2,
                'description' => 'Rapport semestriel H1 2024 de GreenEnergy Ltd.',
            ],
        ];

        foreach ($documents as $data) {
            $document = new \App\Entity\Document();
            $document->setUser($user);
            $document->setName($data['name']);
            $document->setType($data['type']);
            $document->setFilename($data['filename']);
            $document->setFilePath($data['filePath']);
            $document->setMimeType($data['mimeType']);
            $document->setFileSize($data['fileSize']);
            $document->setDocumentDate($data['documentDate']);
            $document->setDescription($data['description']);
            
            if (isset($data['relatedEntity'])) {
                $document->setRelatedEntity($data['relatedEntity']);
            }
            
            if (isset($data['relatedEntityId'])) {
                $document->setRelatedEntityId($data['relatedEntityId']);
            }

            $this->entityManager->persist($document);
        }

        $this->entityManager->flush();
    }
}
