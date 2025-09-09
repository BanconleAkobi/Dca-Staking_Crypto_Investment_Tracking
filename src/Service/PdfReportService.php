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

        $html = $this->generateHtmlReport($user, $portfolioData, $transactions, $cryptos);

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

    private function generateHtmlReport(User $user, array $portfolioData, array $transactions, array $cryptos): string
    {
        $currentDate = new \DateTime();
        $totalValue = $portfolioData['total_value'] ?? 0;
        $totalInvested = $portfolioData['total_invested'] ?? 0;
        $totalGain = $totalValue - $totalInvested;
        $totalGainPercent = $totalInvested > 0 ? ($totalGain / $totalInvested) * 100 : 0;

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Rapport de Portefeuille - DCA Tracker</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    color: #333;
                }
                .header {
                    text-align: center;
                    border-bottom: 3px solid #667eea;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .header h1 {
                    color: #667eea;
                    margin: 0;
                    font-size: 28px;
                }
                .header p {
                    margin: 5px 0;
                    color: #666;
                }
                .summary {
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: white;
                    padding: 20px;
                    border-radius: 10px;
                    margin-bottom: 30px;
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
                    color: #667eea;
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
                    border-radius: 8px;
                    padding: 15px;
                    background: #f8f9fa;
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
                }
                .crypto-price {
                    font-size: 14px;
                    color: #666;
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
                    color: #666;
                }
                .stat-value {
                    font-weight: bold;
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
                }
                .transactions-table th,
                .transactions-table td {
                    border: 1px solid #e9ecef;
                    padding: 8px;
                    text-align: left;
                }
                .transactions-table th {
                    background: #f8f9fa;
                    font-weight: bold;
                }
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    color: #666;
                    font-size: 12px;
                    border-top: 1px solid #e9ecef;
                    padding-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📊 Rapport de Portefeuille</h1>
                <p>DCA Tracker - Suivi d\'investissements crypto</p>
                <p>Généré le ' . $currentDate->format('d/m/Y à H:i') . '</p>
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
            </div>

            <div class="section">
                <h2>💎 Cryptomonnaies en Portefeuille</h2>
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
                            <div class="crypto-name">' . $crypto->getName() . ' (' . $crypto->getSymbol() . ')</div>
                            <div class="crypto-price">$' . number_format($currentPrice, 2) . '</div>
                        </div>
                        <div class="crypto-stats">
                            <div class="stat">
                                <span class="stat-label">Quantité:</span>
                                <span class="stat-value">' . number_format($totalQuantity, 4) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Valeur actuelle:</span>
                                <span class="stat-value">$' . number_format($currentValue, 2) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Investi:</span>
                                <span class="stat-value">$' . number_format($totalInvested, 2) . '</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Gain/Perte:</span>
                                <span class="stat-value ' . ($gain >= 0 ? 'positive' : 'negative') . '">
                                    $' . number_format($gain, 2) . ' (' . number_format($gainPercent, 2) . '%)
                                </span>
                            </div>
                        </div>
                    </div>';
        }

        $html .= '
                </div>
            </div>

            <div class="section">
                <h2>📈 Historique des Transactions</h2>
                <table class="transactions-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Cryptomonnaie</th>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Prix</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>';

        // Sort transactions by date (newest first)
        usort($transactions, fn($a, $b) => $b->getDate() <=> $a->getDate());

        foreach ($transactions as $transaction) {
            $html .= '
                        <tr>
                            <td>' . $transaction->getDate()->format('d/m/Y') . '</td>
                            <td>' . $transaction->getCrypto()->getName() . '</td>
                            <td>' . ucfirst($transaction->getType()) . '</td>
                            <td>' . number_format($transaction->getQuantity(), 4) . '</td>
                            <td>$' . number_format($transaction->getPrice(), 2) . '</td>
                            <td>$' . number_format($transaction->getQuantity() * $transaction->getPrice(), 2) . '</td>
                        </tr>';
        }

        $html .= '
                    </tbody>
                </table>
            </div>

            <div class="footer">
                <p>Ce rapport a été généré automatiquement par DCA Tracker</p>
                <p>Pour plus d\'informations, visitez votre tableau de bord</p>
            </div>
        </body>
        </html>';

        return $html;
    }
}
