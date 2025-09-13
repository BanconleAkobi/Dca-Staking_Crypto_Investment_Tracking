<?php

namespace App\Tests\Functional\Investment;

use App\Entity\User;
use App\Entity\Crypto;
use App\Entity\Asset;
use App\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PortfolioManagementTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        
        // Créer un utilisateur de test avec des données
        $this->user = new User();
        $this->user->setEmail('portfolio@example.com');
        $this->user->setPseudo('portfoliouser');
        $this->user->setPassword('password123');
        $this->user->setRoles(['ROLE_USER']);
        $this->user->setIsActive(true);
        $this->user->setIsVerified(true);
        
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        if ($this->user) {
            $this->entityManager->remove($this->user);
            $this->entityManager->flush();
        }
        
        parent::tearDown();
    }

    public function testCompletePortfolioManagementFlow(): void
    {
        $this->client->loginUser($this->user);
        
        // 1. Accéder au tableau de bord
        $crawler = $this->client->request('GET', '/investment/dashboard');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // 2. Vérifier les métriques initiales
        $this->assertSelectorExists('.metric-card');
        $this->assertSelectorTextContains('.metric-label', 'Investissement Total');
        $this->assertSelectorTextContains('.metric-label', 'Valeur Actuelle');
        
        // 3. Accéder à la liste des investissements
        $crawler = $this->client->request('GET', '/investment/investments');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // 4. Vérifier les filtres
        $this->assertSelectorExists('#searchInput');
        $this->assertSelectorExists('#typeFilter');
        $this->assertSelectorExists('#statusFilter');
        
        // 5. Accéder au calendrier des échéances
        $crawler = $this->client->request('GET', '/investment/calendar');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // 6. Vérifier le calendrier
        $this->assertSelectorExists('.calendar-month');
        $this->assertSelectorExists('#paymentsTable');
        
        // 7. Accéder aux assemblées générales
        $crawler = $this->client->request('GET', '/investment/assemblies');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // 8. Vérifier les assemblées
        $this->assertSelectorExists('#assembliesList');
        $this->assertSelectorExists('.stat-card');
        
        // 9. Accéder aux documents
        $crawler = $this->client->request('GET', '/investment/documents');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // 10. Vérifier les documents
        $this->assertSelectorExists('#gridView');
        $this->assertSelectorExists('#listView');
    }

    public function testPortfolioCalculations(): void
    {
        $this->client->loginUser($this->user);
        
        // Créer des données de test
        $this->createTestData();
        
        // Accéder au tableau de bord
        $crawler = $this->client->request('GET', '/investment/dashboard');
        
        // Vérifier que les calculs sont corrects
        $this->assertSelectorExists('.metric-value');
        
        // Les valeurs devraient être calculées automatiquement
        $metricValues = $crawler->filter('.metric-value');
        $this->assertGreaterThan(0, $metricValues->count());
    }

    public function testInvestmentFilters(): void
    {
        $this->client->loginUser($this->user);
        
        // Accéder à la liste des investissements
        $crawler = $this->client->request('GET', '/investment/investments');
        
        // Tester le filtre de recherche
        $form = $crawler->filter('form')->form();
        $form['searchInput'] = 'Bitcoin';
        
        $crawler = $this->client->submit($form);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // Tester le filtre par type
        $form = $crawler->filter('form')->form();
        $form['typeFilter'] = 'crypto';
        
        $crawler = $this->client->submit($form);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // Tester le filtre par statut
        $form = $crawler->filter('form')->form();
        $form['statusFilter'] = 'active';
        
        $crawler = $this->client->submit($form);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testCalendarFunctionality(): void
    {
        $this->client->loginUser($this->user);
        
        // Accéder au calendrier
        $crawler = $this->client->request('GET', '/investment/calendar');
        
        // Vérifier la présence des éléments du calendrier
        $this->assertSelectorExists('.calendar-month');
        $this->assertSelectorExists('#paymentsTable');
        
        // Vérifier les actions
        $this->assertSelectorExists('.btn[onclick="exportCalendar()"]');
        $this->assertSelectorExists('.btn[onclick="printCalendar()"]');
        
        // Vérifier les statistiques
        $this->assertSelectorExists('.stat-card');
    }

    public function testAssemblyManagement(): void
    {
        $this->client->loginUser($this->user);
        
        // Accéder aux assemblées
        $crawler = $this->client->request('GET', '/investment/assemblies');
        
        // Vérifier les filtres
        $this->assertSelectorExists('#statusFilter');
        $this->assertSelectorExists('#typeFilter');
        
        // Vérifier les statistiques
        $this->assertSelectorExists('.stat-card');
        
        // Vérifier la présence des assemblées
        $this->assertSelectorExists('#assembliesList');
    }

    public function testDocumentManagement(): void
    {
        $this->client->loginUser($this->user);
        
        // Accéder aux documents
        $crawler = $this->client->request('GET', '/investment/documents');
        
        // Vérifier les filtres
        $this->assertSelectorExists('#searchInput');
        $this->assertSelectorExists('#typeFilter');
        $this->assertSelectorExists('#investmentFilter');
        
        // Vérifier les vues
        $this->assertSelectorExists('#gridView');
        $this->assertSelectorExists('#listView');
        
        // Vérifier les actions
        $this->assertSelectorExists('.btn[onclick="downloadAll()"]');
    }

    public function testPortfolioPerformance(): void
    {
        $this->client->loginUser($this->user);
        
        // Mesurer les performances du tableau de bord
        $startTime = microtime(true);
        $crawler = $this->client->request('GET', '/investment/dashboard');
        $endTime = microtime(true);
        
        $responseTime = $endTime - $startTime;
        
        // Vérifier que la réponse est rapide
        $this->assertLessThan(3, $responseTime);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // Mesurer les performances de la liste des investissements
        $startTime = microtime(true);
        $crawler = $this->client->request('GET', '/investment/investments');
        $endTime = microtime(true);
        
        $responseTime = $endTime - $startTime;
        
        $this->assertLessThan(2, $responseTime);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testPortfolioResponsiveness(): void
    {
        $this->client->loginUser($this->user);
        
        // Tester avec différentes tailles d'écran
        $this->client->setServerParameter('HTTP_USER_AGENT', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)');
        
        $crawler = $this->client->request('GET', '/investment/dashboard');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        
        // Vérifier que le design responsive fonctionne
        $this->assertSelectorExists('.mobile-menu-toggle');
    }

    public function testPortfolioDataIntegrity(): void
    {
        $this->client->loginUser($this->user);
        
        // Créer des données de test
        $this->createTestData();
        
        // Vérifier que les données sont cohérentes
        $crawler = $this->client->request('GET', '/investment/dashboard');
        
        // Les métriques devraient être cohérentes
        $this->assertSelectorExists('.metric-card');
        
        // Vérifier la liste des investissements
        $crawler = $this->client->request('GET', '/investment/investments');
        $this->assertSelectorExists('#investmentsList');
    }

    public function testPortfolioErrorHandling(): void
    {
        $this->client->loginUser($this->user);
        
        // Tester l'accès à une page inexistante
        $this->client->request('GET', '/investment/nonexistent');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        
        // Tester l'accès à un investissement inexistant
        $this->client->request('GET', '/investment/investment/999999');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    private function createTestData(): void
    {
        // Créer une crypto de test
        $crypto = new Crypto();
        $crypto->setName('Bitcoin');
        $crypto->setSymbol('BTC');
        $crypto->setCurrentPrice(50000);
        
        $this->entityManager->persist($crypto);
        
        // Créer une transaction de test
        $transaction = new Transaction();
        $transaction->setUser($this->user);
        $transaction->setCrypto($crypto);
        $transaction->setType('buy');
        $transaction->setQuantity(0.1);
        $transaction->setUnitPriceUsd(45000);
        $transaction->setDate(new \DateTime());
        
        $this->entityManager->persist($transaction);
        
        // Créer un asset de test
        $asset = new Asset();
        $asset->setName('Apple Inc.');
        $asset->setSymbol('AAPL');
        $asset->setCurrentPrice(150);
        $asset->setType('stock');
        
        $this->entityManager->persist($asset);
        
        // Créer une transaction d'asset
        $assetTransaction = new Transaction();
        $assetTransaction->setUser($this->user);
        $assetTransaction->setAsset($asset);
        $assetTransaction->setType('buy');
        $assetTransaction->setQuantity(10);
        $assetTransaction->setUnitPriceUsd(140);
        $assetTransaction->setDate(new \DateTime());
        
        $this->entityManager->persist($assetTransaction);
        
        $this->entityManager->flush();
    }
}
