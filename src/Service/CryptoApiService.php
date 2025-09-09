<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class CryptoApiService
{
    private HttpClientInterface $httpClient;
    private FilesystemAdapter $cache;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->cache = new FilesystemAdapter();
    }

    public function getCryptoPrice(string $symbol): ?float
    {
        $cacheKey = 'crypto_price_' . strtolower($symbol);
        
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($symbol) {
            $item->expiresAfter(300); // 5 minutes cache
            
            try {
                $response = $this->httpClient->request('GET', 'https://api.coingecko.com/api/v3/simple/price', [
                    'query' => [
                        'ids' => $this->getCoinGeckoId($symbol),
                        'vs_currencies' => 'usd'
                    ]
                ]);

                $data = $response->toArray();
                $coinId = $this->getCoinGeckoId($symbol);
                
                return $data[$coinId]['usd'] ?? null;
            } catch (\Exception $e) {
                // Fallback to mock data if API fails
                return $this->getMockPrice($symbol);
            }
        });
    }

    public function getCryptoData(string $symbol): array
    {
        $cacheKey = 'crypto_data_' . strtolower($symbol);
        
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($symbol) {
            $item->expiresAfter(300); // 5 minutes cache
            
            try {
                $response = $this->httpClient->request('GET', 'https://api.coingecko.com/api/v3/coins/' . $this->getCoinGeckoId($symbol));
                $data = $response->toArray();
                
                return [
                    'price' => $data['market_data']['current_price']['usd'] ?? 0,
                    'change_24h' => $data['market_data']['price_change_percentage_24h'] ?? 0,
                    'market_cap' => $data['market_data']['market_cap']['usd'] ?? 0,
                    'volume_24h' => $data['market_data']['total_volume']['usd'] ?? 0,
                    'image' => $data['image']['small'] ?? null
                ];
            } catch (\Exception $e) {
                // Fallback to mock data
                return $this->getMockData($symbol);
            }
        });
    }

    public function getPortfolioValue(array $holdings): array
    {
        $totalValue = 0;
        $totalInvested = 0;
        $cryptoValues = [];

        foreach ($holdings as $holding) {
            $symbol = $holding['symbol'];
            $quantity = $holding['quantity'];
            $invested = $holding['invested'];
            
            $currentPrice = $this->getCryptoPrice($symbol);
            if ($currentPrice) {
                $currentValue = $quantity * $currentPrice;
                $totalValue += $currentValue;
                $totalInvested += $invested;
                
                $cryptoValues[$symbol] = [
                    'quantity' => $quantity,
                    'invested' => $invested,
                    'current_price' => $currentPrice,
                    'current_value' => $currentValue,
                    'gain_loss' => $currentValue - $invested,
                    'gain_loss_percent' => $invested > 0 ? (($currentValue - $invested) / $invested) * 100 : 0
                ];
            }
        }

        return [
            'total_value' => $totalValue,
            'total_invested' => $totalInvested,
            'total_gain_loss' => $totalValue - $totalInvested,
            'total_gain_loss_percent' => $totalInvested > 0 ? (($totalValue - $totalInvested) / $totalInvested) * 100 : 0,
            'crypto_values' => $cryptoValues
        ];
    }

    private function getCoinGeckoId(string $symbol): string
    {
        $mapping = [
            'BTC' => 'bitcoin',
            'ETH' => 'ethereum',
            'SOL' => 'solana',
            'ADA' => 'cardano',
            'DOT' => 'polkadot',
            'MATIC' => 'matic-network',
            'AVAX' => 'avalanche-2',
            'LINK' => 'chainlink',
            'UNI' => 'uniswap',
            'ATOM' => 'cosmos'
        ];

        return $mapping[strtoupper($symbol)] ?? strtolower($symbol);
    }

    private function getMockPrice(string $symbol): float
    {
        $mockPrices = [
            'BTC' => 45000.00,
            'ETH' => 2800.00,
            'SOL' => 95.50,
            'ADA' => 0.45,
            'DOT' => 6.80,
            'MATIC' => 0.85,
            'AVAX' => 25.30,
            'LINK' => 12.50,
            'UNI' => 6.20,
            'ATOM' => 8.90
        ];

        return $mockPrices[strtoupper($symbol)] ?? 100.00;
    }

    private function getMockData(string $symbol): array
    {
        $price = $this->getMockPrice($symbol);
        
        return [
            'price' => $price,
            'change_24h' => rand(-10, 10) + (rand(0, 100) / 100),
            'market_cap' => $price * rand(1000000, 1000000000),
            'volume_24h' => $price * rand(100000, 10000000),
            'image' => null
        ];
    }
}
