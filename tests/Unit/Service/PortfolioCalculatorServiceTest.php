<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Entity\Crypto;
use App\Entity\Asset;
use App\Entity\Transaction;
use App\Service\PortfolioCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PortfolioCalculatorServiceTest extends TestCase
{
    private PortfolioCalculatorService $service;
    private MockObject|EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->service = new PortfolioCalculatorService($this->entityManager);
    }

    public function testCalculatePortfolioWithNoInvestments(): void
    {
        $user = new User();
        
        $result = $this->service->calculatePortfolio($user);
        
        $this->assertIsArray($result);
        $this->assertEquals(0, $result['total_invested']);
        $this->assertEquals(0, $result['total_value']);
        $this->assertEquals(0, $result['total_gain']);
        $this->assertEquals(0, $result['total_gain_percent']);
    }

    public function testCalculatePortfolioWithCryptoInvestments(): void
    {
        $user = new User();
        
        // Créer une crypto avec des transactions
        $crypto = new Crypto();
        $crypto->setName('Bitcoin');
        $crypto->setSymbol('BTC');
        $crypto->setCurrentPrice(50000);
        
        // Créer des transactions
        $transaction1 = new Transaction();
        $transaction1->setCrypto($crypto);
        $transaction1->setType('buy');
        $transaction1->setQuantity(0.1);
        $transaction1->setUnitPriceUsd(45000);
        $transaction1->setDate(new \DateTime());
        
        $transaction2 = new Transaction();
        $transaction2->setCrypto($crypto);
        $transaction2->setType('buy');
        $transaction2->setQuantity(0.05);
        $transaction2->setUnitPriceUsd(48000);
        $transaction2->setDate(new \DateTime());
        
        $user->addTransaction($transaction1);
        $user->addTransaction($transaction2);
        $user->addCrypto($crypto);
        
        $result = $this->service->calculatePortfolio($user);
        
        // Calculs attendus :
        // Investi : (0.1 * 45000) + (0.05 * 48000) = 4500 + 2400 = 6900
        // Quantité totale : 0.1 + 0.05 = 0.15
        // Valeur actuelle : 0.15 * 50000 = 7500
        // Gain : 7500 - 6900 = 600
        // Gain % : (600 / 6900) * 100 = 8.70%
        
        $this->assertEquals(6900, $result['total_invested']);
        $this->assertEquals(7500, $result['total_value']);
        $this->assertEquals(600, $result['total_gain']);
        $this->assertEqualsWithDelta(8.70, $result['total_gain_percent'], 0.01);
    }

    public function testCalculatePortfolioWithAssetInvestments(): void
    {
        $user = new User();
        
        // Créer un asset avec des transactions
        $asset = new Asset();
        $asset->setName('Apple Inc.');
        $asset->setSymbol('AAPL');
        $asset->setCurrentPrice(150);
        $asset->setType('stock');
        
        // Créer des transactions
        $transaction = new Transaction();
        $transaction->setAsset($asset);
        $transaction->setType('buy');
        $transaction->setQuantity(10);
        $transaction->setUnitPriceUsd(140);
        $transaction->setDate(new \DateTime());
        
        $user->addTransaction($transaction);
        $user->addAsset($asset);
        
        $result = $this->service->calculatePortfolio($user);
        
        // Calculs attendus :
        // Investi : 10 * 140 = 1400
        // Valeur actuelle : 10 * 150 = 1500
        // Gain : 1500 - 1400 = 100
        // Gain % : (100 / 1400) * 100 = 7.14%
        
        $this->assertEquals(1400, $result['total_invested']);
        $this->assertEquals(1500, $result['total_value']);
        $this->assertEquals(100, $result['total_gain']);
        $this->assertEqualsWithDelta(7.14, $result['total_gain_percent'], 0.01);
    }

    public function testCalculatePortfolioWithMixedInvestments(): void
    {
        $user = new User();
        
        // Crypto
        $crypto = new Crypto();
        $crypto->setName('Bitcoin');
        $crypto->setSymbol('BTC');
        $crypto->setCurrentPrice(50000);
        
        $cryptoTransaction = new Transaction();
        $cryptoTransaction->setCrypto($crypto);
        $cryptoTransaction->setType('buy');
        $cryptoTransaction->setQuantity(0.1);
        $cryptoTransaction->setUnitPriceUsd(45000);
        $cryptoTransaction->setDate(new \DateTime());
        
        // Asset
        $asset = new Asset();
        $asset->setName('Apple Inc.');
        $asset->setSymbol('AAPL');
        $asset->setCurrentPrice(150);
        $asset->setType('stock');
        
        $assetTransaction = new Transaction();
        $assetTransaction->setAsset($asset);
        $assetTransaction->setType('buy');
        $assetTransaction->setQuantity(10);
        $assetTransaction->setUnitPriceUsd(140);
        $assetTransaction->setDate(new \DateTime());
        
        $user->addTransaction($cryptoTransaction);
        $user->addTransaction($assetTransaction);
        $user->addCrypto($crypto);
        $user->addAsset($asset);
        
        $result = $this->service->calculatePortfolio($user);
        
        // Calculs attendus :
        // Investi : (0.1 * 45000) + (10 * 140) = 4500 + 1400 = 5900
        // Valeur actuelle : (0.1 * 50000) + (10 * 150) = 5000 + 1500 = 6500
        // Gain : 6500 - 5900 = 600
        // Gain % : (600 / 5900) * 100 = 10.17%
        
        $this->assertEquals(5900, $result['total_invested']);
        $this->assertEquals(6500, $result['total_value']);
        $this->assertEquals(600, $result['total_gain']);
        $this->assertEqualsWithDelta(10.17, $result['total_gain_percent'], 0.01);
    }

    public function testCalculatePortfolioWithLoss(): void
    {
        $user = new User();
        
        $crypto = new Crypto();
        $crypto->setName('Bitcoin');
        $crypto->setSymbol('BTC');
        $crypto->setCurrentPrice(40000); // Prix actuel plus bas
        
        $transaction = new Transaction();
        $transaction->setCrypto($crypto);
        $transaction->setType('buy');
        $transaction->setQuantity(0.1);
        $transaction->setUnitPriceUsd(50000); // Prix d'achat plus élevé
        $transaction->setDate(new \DateTime());
        
        $user->addTransaction($transaction);
        $user->addCrypto($crypto);
        
        $result = $this->service->calculatePortfolio($user);
        
        // Calculs attendus :
        // Investi : 0.1 * 50000 = 5000
        // Valeur actuelle : 0.1 * 40000 = 4000
        // Gain : 4000 - 5000 = -1000 (perte)
        // Gain % : (-1000 / 5000) * 100 = -20%
        
        $this->assertEquals(5000, $result['total_invested']);
        $this->assertEquals(4000, $result['total_value']);
        $this->assertEquals(-1000, $result['total_gain']);
        $this->assertEquals(-20, $result['total_gain_percent']);
    }

    public function testCalculatePortfolioWithZeroInvestment(): void
    {
        $user = new User();
        
        $crypto = new Crypto();
        $crypto->setName('Bitcoin');
        $crypto->setSymbol('BTC');
        $crypto->setCurrentPrice(50000);
        
        $transaction = new Transaction();
        $transaction->setCrypto($crypto);
        $transaction->setType('buy');
        $transaction->setQuantity(0);
        $transaction->setUnitPriceUsd(50000);
        $transaction->setDate(new \DateTime());
        
        $user->addTransaction($transaction);
        $user->addCrypto($crypto);
        
        $result = $this->service->calculatePortfolio($user);
        
        $this->assertEquals(0, $result['total_invested']);
        $this->assertEquals(0, $result['total_value']);
        $this->assertEquals(0, $result['total_gain']);
        $this->assertEquals(0, $result['total_gain_percent']);
    }

    public function testCalculatePortfolioWithSellTransactions(): void
    {
        $user = new User();
        
        $crypto = new Crypto();
        $crypto->setName('Bitcoin');
        $crypto->setSymbol('BTC');
        $crypto->setCurrentPrice(50000);
        
        // Achat
        $buyTransaction = new Transaction();
        $buyTransaction->setCrypto($crypto);
        $buyTransaction->setType('buy');
        $buyTransaction->setQuantity(0.2);
        $buyTransaction->setUnitPriceUsd(45000);
        $buyTransaction->setDate(new \DateTime());
        
        // Vente partielle
        $sellTransaction = new Transaction();
        $sellTransaction->setCrypto($crypto);
        $sellTransaction->setType('sell');
        $sellTransaction->setQuantity(0.1);
        $sellTransaction->setUnitPriceUsd(50000);
        $sellTransaction->setDate(new \DateTime());
        
        $user->addTransaction($buyTransaction);
        $user->addTransaction($sellTransaction);
        $user->addCrypto($crypto);
        
        $result = $this->service->calculatePortfolio($user);
        
        // Calculs attendus :
        // Investi : (0.2 * 45000) - (0.1 * 50000) = 9000 - 5000 = 4000
        // Quantité restante : 0.2 - 0.1 = 0.1
        // Valeur actuelle : 0.1 * 50000 = 5000
        // Gain : 5000 - 4000 = 1000
        // Gain % : (1000 / 4000) * 100 = 25%
        
        $this->assertEquals(4000, $result['total_invested']);
        $this->assertEquals(5000, $result['total_value']);
        $this->assertEquals(1000, $result['total_gain']);
        $this->assertEquals(25, $result['total_gain_percent']);
    }
}
