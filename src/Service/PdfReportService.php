<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Transaction;
use App\Entity\Crypto;
use App\Service\PortfolioCalculatorService;
use App\Service\CryptoApiService;
use Dompdf\Dompdf;
use Dompdf\Options;

class PdfReportService
{
    public function __construct(
        private PortfolioCalculatorService $portfolioCalculator,
        private CryptoApiService $cryptoApiService
    ) {}

    public function generatePortfolioReport(User $user): string
    {
        $portfolioData = $this->portfolioCalculator->calculatePortfolio($user);
        $transactions = $user->getTransactions()->toArray();
        $cryptos = $user->getCryptos()->toArray();
        $assets = $user->getAssets()->toArray();
        $savingsAccounts = $user->getSavingsAccounts()->toArray();

        $html = $this->generateHtmlReport($user, $portfolioData, $transactions, $cryptos, $assets, $savingsAccounts);

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function generateHtmlReport(User $user, array $portfolioData, array $transactions, array $cryptos, array $assets, array $savingsAccounts): string
    {
        $currentDate = new \DateTime();
        $totalValue = $portfolioData['total_value'] ?? 0;
        $totalInvested = $portfolioData['total_invested'] ?? 0;
        $totalGain = $totalValue - $totalInvested;
        $totalGainPercent = $totalInvested > 0 ? ($totalGain / $totalInvested) * 100 : 0;

        $html = '
        <!DOCTYPE html>
        <html style="background: white; color: #2c3e50;">
        <head>
            <meta charset="UTF-8">
            <title>Rapport de Portefeuille - Crypto Investment Tracker</title>
            <style>
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    margin: 0;
                    padding: 20px;
                    color: #2c3e50 !important;
                    background: white !important;
                    line-height: 1.6;
                }
                .header {
                    text-align: center;
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: white;
                    padding: 30px;
                    border-radius: 15px;
                    margin-bottom: 30px;
                    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
                }
                .header h1 {
                    color: white;
                    margin: 0;
                    font-size: 32px;
                    font-weight: 700;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
                }
                .header p {
                    margin: 8px 0;
                    color: rgba(255,255,255,0.9);
                    font-size: 16px;
                }
                .summary {
                    background: linear-gradient(135deg, #2c3e50, #34495e);
                    color: white;
                    padding: 25px;
                    border-radius: 15px;
                    margin-bottom: 30px;
                    box-shadow: 0 8px 25px rgba(44, 62, 80, 0.2);
                }
                .summary-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                }
                .summary-item {
                    text-align: center;
                }
                .summary-item h3 {
                    margin: 0 0 5px 0;
                    font-size: 14px;
                    opacity: 0.9;
                }
                .summary-item .value {
                    font-size: 24px;
                    font-weight: bold;
                    margin: 0;
                }
                .section {
                    margin-bottom: 30px;
                }
                .section h2 {
                    color: #667eea !important;
                    border-bottom: 2px solid #e9ecef;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }
                .crypto-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                }
                .crypto-card {
                    border: 1px solid #e9ecef;
                    border-radius: 12px;
                    padding: 20px;
                    background: white;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    transition: transform 0.2s ease;
                }
                .crypto-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 10px;
                }
                .crypto-name {
                    font-weight: bold;
                    font-size: 16px;
                    color: #2c3e50 !important;
                }
                .crypto-price {
                    font-size: 14px;
                    color: #666 !important;
                }
                .crypto-stats {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 10px;
                    font-size: 12px;
                }
                .stat {
                    display: flex;
                    justify-content: space-between;
                }
                .stat-label {
                    color: #666 !important;
                }
                .stat-value {
                    font-weight: bold;
                    color: #2c3e50 !important;
                }
                .positive {
                    color: #28a745;
                }
                .negative {
                    color: #dc3545;
                }
                .transactions-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                }
                .transactions-table th,
                .transactions-table td {
                    border: 1px solid #e9ecef;
                    padding: 12px;
                    text-align: left;
                    color: #2c3e50 !important;
                }
                .transactions-table th {
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: white !important;
                    font-weight: 600;
                    text-transform: uppercase;
                    font-size: 12px;
                    letter-spacing: 0.5px;
                }
                .transactions-table tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .transactions-table tr:hover {
                    background: #e3f2fd;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    color: #666 !important;
                    font-size: 12px;
                    border-top: 2px solid #667eea;
                    padding-top: 20px;
                    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                    padding: 20px;
                    border-radius: 10px;
                }
            </style>
        </head>
        <body style="background: white; color: #2c3e50;">
            <div class="header">
                <h1>📊 Rapport de Portefeuille</h1>
                <p>Crypto Investment Tracker - Suivi d\'investissements crypto</p>
                <p><strong>Utilisateur:</strong> ' . $user->getEmail() . '</p>
                <p><strong>Généré le:</strong> ' . $currentDate->format('d/m/Y à H:i') . '</p>
            </div>

            <div class="summary">
                <h2 style="margin-top: 0; text-align: center;">Résumé du Portefeuille</h2>
                <div class="summary-grid">
                    <div class="summary-item">
                        <h3>Valeur Totale</h3>
                        <p class="value">$' . number_format($totalValue, 2) . '</p>
                    </div>
                    <div class="summary-item">
                        <h3>Investissement Total</h3>
                        <p class="value">$' . number_format($totalInvested, 2) . '</p>
                    </div>
                    <div class="summary-item">
                        <h3>Gain/Perte</h3>
                        <p class="value ' . ($totalGain >= 0 ? 'positive' : 'negative') . '">
                            $' . number_format($totalGain, 2) . '
                        </p>
                    </div>
                    <div class="summary-item">
                        <h3>Performance</h3>
                        <p class="value ' . ($totalGainPercent >= 0 ? 'positive' : 'negative') . '">
                            ' . number_format($totalGainPercent, 2) . '%
                        </p>
                    </div>
                </div>
                <div class="summary-grid" style="margin-top: 20px;">
                    <div class="summary-item">
                        <h3>Cryptomonnaies</h3>
                        <p class="value">' . count($cryptos) . '</p>
                    </div>
                    <div class="summary-item">
                        <h3>Actifs</h3>
                        <p class="value">' . count($assets) . '</p>
                    </div>
                    <div class="summary-item">
                        <h3>Comptes Épargne</h3>
                        <p class="value">' . count($savingsAccounts) . '</p>
                    </div>
                    <div class="summary-item">
                        <h3>Transactions</h3>
                        <p class="value">' . count($transactions) . '</p>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2 style="color: #667eea;">💎 Cryptomonnaies en Portefeuille</h2>
                <div class="crypto-grid">';

        foreach ($cryptos as $crypto) {
            $cryptoData = $this->cryptoApiService->getCryptoData($crypto->getSymbol());
            $currentPrice = $cryptoData['current_price'] ?? 0;
            $userTransactions = array_filter($transactions, fn($t) => $t->getCrypto()->getId() === $crypto->getId());
            $totalQuantity = array_sum(array_map(fn($t) => $t->getQuantity(), $userTransactions));
            $totalInvested = array_sum(array_map(fn($t) => $t->getQuantity() * $t->getPrice(), $userTransactions));
            $currentValue = $totalQuantity * $currentPrice;
            $gain = $currentValue - $totalInvested;
            $gainPercent = $totalInvested > 0 ? ($gain / $totalInvested) * 100 : 0;

            $html .= '
                    <div class="crypto-card">
                        <div class="crypto-header">
                            <div class="crypto-name" style="color: #2c3e50;">' . $crypto->getName() . ' (' . $crypto->getSymbol() . ')</div>
                            <div class="crypto-price" style="color: #666;">$' . number_format($currentPrice, 2) . '</div>
                        </div>
                        <div class="crypto-stats">
                            <div class="stat">
                                <span class="stat-label" style="color: #666;">Quantité:</span>
                                <span class="stat-value" style="color: #2c3e50;">' . number_format($totalQuantity, 4) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label" style="color: #666;">Valeur actuelle:</span>
                                <span class="stat-value" style="color: #2c3e50;">$' . number_format($currentValue, 2) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label" style="color: #666;">Investi:</span>
                                <span class="stat-value" style="color: #2c3e50;">$' . number_format($totalInvested, 2) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label" style="color: #666;">Gain/Perte:</span>
                                <span class="stat-value" style="color: ' . ($gain >= 0 ? '#28a745' : '#dc3545') . ';">
                                    $' . number_format($gain, 2) . ' (' . number_format($gainPercent, 2) . '%)
                                </span>
                            </div>
                        </div>
                    </div>';
        }

        $html .= '
                </div>
            </div>';

        // Section Actifs
        if (!empty($assets)) {
            $html .= '
            <div class="section">
                <h2 style="color: #667eea;">📊 Actifs en Portefeuille</h2>
                <div class="crypto-grid">';

            foreach ($assets as $asset) {
                $html .= '
                    <div class="crypto-card">
                        <div class="crypto-header">
                            <div class="crypto-name">' . $asset->getName() . ' (' . $asset->getSymbol() . ')</div>
                            <div class="crypto-price">$' . number_format($asset->getCurrentPrice(), 2) . '</div>
                        </div>
                        <div class="crypto-stats">
                            <div class="stat">
                                <span class="stat-label">Type:</span>
                                <span class="stat-value">' . ucfirst($asset->getType()) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Catégorie:</span>
                                <span class="stat-value">' . ucfirst($asset->getCategory()) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Devise:</span>
                                <span class="stat-value">' . strtoupper($asset->getCurrency()) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Statut:</span>
                                <span class="stat-value">' . ($asset->isActive() ? 'Actif' : 'Inactif') . '</span>
                            </div>
                        </div>
                    </div>';
            }

            $html .= '
                </div>
            </div>';
        }

        // Section Comptes d'épargne
        if (!empty($savingsAccounts)) {
            $html .= '
            <div class="section">
                <h2 style="color: #667eea;">🏦 Comptes d\'Épargne</h2>
                <div class="crypto-grid">';

            foreach ($savingsAccounts as $account) {
                $html .= '
                    <div class="crypto-card">
                        <div class="crypto-header">
                            <div class="crypto-name">' . $account->getName() . '</div>
                            <div class="crypto-price">€' . number_format($account->getCurrentBalance(), 2) . '</div>
                        </div>
                        <div class="crypto-stats">
                            <div class="stat">
                                <span class="stat-label">Banque:</span>
                                <span class="stat-value">' . $account->getBankName() . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Type:</span>
                                <span class="stat-value">' . ucfirst($account->getType()) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Taux annuel:</span>
                                <span class="stat-value">' . number_format($account->getAnnualRate(), 2) . '%</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Plafond:</span>
                                <span class="stat-value">€' . number_format($account->getMaxAmount(), 2) . '</span>
                            </div>
                        </div>
                    </div>';
            }

            $html .= '
                </div>
            </div>';
        }

        $html .= '
            <div class="section">
                <h2 style="color: #667eea;">📈 Historique des Transactions</h2>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Actif</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                            <th>Total</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>';

        // Sort transactions by date (newest first)
        usort($transactions, fn($a, $b) => $b->getDate() <=> $a->getDate());

        foreach ($transactions as $transaction) {
            // Déterminer le nom de l'actif
            $assetName = '';
            if ($transaction->getCrypto()) {
                $assetName = $transaction->getCrypto()->getName() . ' (Crypto)';
            } elseif ($transaction->getAsset()) {
                $assetName = $transaction->getAsset()->getName() . ' (' . ucfirst($transaction->getAsset()->getType()) . ')';
            } elseif ($transaction->getSavingsAccount()) {
                $assetName = $transaction->getSavingsAccount()->getName() . ' (Épargne)';
            } else {
                $assetName = 'Actif inconnu';
            }

            $html .= '
                        <tr>
                            <td style="color: #2c3e50;">' . $transaction->getDate()->format('d/m/Y') . '</td>
                            <td style="color: #2c3e50;">' . $assetName . '</td>
                            <td style="color: #2c3e50;">' . ucfirst($transaction->getType()) . '</td>
                            <td style="color: #2c3e50;">' . number_format($transaction->getQuantity(), 4) . '</td>
                            <td style="color: #2c3e50;">$' . number_format($transaction->getUnitPriceUsd(), 2) . '</td>
                            <td style="color: #2c3e50;">$' . number_format($transaction->getQuantity() * $transaction->getUnitPriceUsd(), 2) . '</td>
                            <td style="color: #2c3e50;">' . ($transaction->getNote() ?: '-') . '</td>
                        </tr>';
        }

        $html .= '
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <p>Ce rapport a été généré automatiquement par Crypto Investment Tracker</p>
                <p>Pour plus d\'informations, visitez votre tableau de bord</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
