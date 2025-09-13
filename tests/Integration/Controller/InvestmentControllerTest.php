<?php

namespace App\Tests\Integration\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InvestmentControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        
        // Créer un utilisateur de test
        $this->user = new User();
        $this->user->setEmail('test@example.com');
        $this->user->setPseudo('testuser');
        $this->user->setPassword('password123');
        $this->user->setRoles(['ROLE_USER']);
        $this->user->setIsActive(true);
        $this->user->setIsVerified(true);
        
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        // Nettoyer la base de données
        if ($this->user) {
            $this->entityManager->remove($this->user);
            $this->entityManager->flush();
        }
        
        parent::tearDown();
    }

    public function testInvestmentDashboardAccess(): void
    {
        // Simuler la connexion
        $this->client->loginUser($this->user);
        
        // Accéder au tableau de bord
        $this->client->request('GET', '/investment/dashboard');
        
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Tableau de Bord Investissement');
    }

    public function testInvestmentDashboardWithoutLogin(): void
    {
        // Essayer d'accéder sans être connecté
        $this->client->request('GET', '/investment/dashboard');
        
        // Devrait rediriger vers la page de connexion
        $this->assertResponseRedirects();
    }

    public function testInvestmentListAccess(): void
    {
        $this->client->loginUser($this->user);
        
        $this->client->request('GET', '/investment/investments');
        
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Mes Investissements');
    }

    public function testInvestmentDetailAccess(): void
    {
        $this->client->loginUser($this->user);
        
        // Essayer d'accéder à un investissement inexistant
        $this->client->request('GET', '/investment/investment/nonexistent');
        
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testInvestmentCalendarAccess(): void
    {
        $this->client->loginUser($this->user);
        
        $this->client->request('GET', '/investment/calendar');
        
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Calendrier des Échéances');
    }

    public function testInvestmentAssembliesAccess(): void
    {
        $this->client->loginUser($this->user);
        
        $this->client->request('GET', '/investment/assemblies');
        
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Assemblées Générales');
    }

    public function testInvestmentDocumentsAccess(): void
    {
        $this->client->loginUser($this->user);
        
        $this->client->request('GET', '/investment/documents');
        
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Documents');
    }

    public function testInvestmentDashboardContent(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/dashboard');
        
        // Vérifier la présence des éléments clés
        $this->assertSelectorExists('.metric-card');
        $this->assertSelectorExists('.stat-card');
        $this->assertSelectorExists('#monthlyEvolutionChart');
        $this->assertSelectorExists('#sectorChart');
        $this->assertSelectorExists('#assetTypeChart');
    }

    public function testInvestmentListContent(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/investments');
        
        // Vérifier la présence des filtres
        $this->assertSelectorExists('#searchInput');
        $this->assertSelectorExists('#typeFilter');
        $this->assertSelectorExists('#statusFilter');
        
        // Vérifier la présence des actions rapides
        $this->assertSelectorExists('.btn[onclick="resetFilters()"]');
    }

    public function testInvestmentCalendarContent(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/calendar');
        
        // Vérifier la présence des éléments du calendrier
        $this->assertSelectorExists('.calendar-month');
        $this->assertSelectorExists('#calendarView');
        $this->assertSelectorExists('#paymentsTable');
        
        // Vérifier les actions
        $this->assertSelectorExists('.btn[onclick="exportCalendar()"]');
        $this->assertSelectorExists('.btn[onclick="printCalendar()"]');
    }

    public function testInvestmentAssembliesContent(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/assemblies');
        
        // Vérifier la présence des filtres
        $this->assertSelectorExists('#statusFilter');
        $this->assertSelectorExists('#typeFilter');
        
        // Vérifier la présence des statistiques
        $this->assertSelectorExists('.stat-card');
    }

    public function testInvestmentDocumentsContent(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/documents');
        
        // Vérifier la présence des filtres
        $this->assertSelectorExists('#searchInput');
        $this->assertSelectorExists('#typeFilter');
        $this->assertSelectorExists('#investmentFilter');
        
        // Vérifier la présence des vues
        $this->assertSelectorExists('#gridView');
        $this->assertSelectorExists('#listView');
    }

    public function testInvestmentDashboardPerformance(): void
    {
        $this->client->loginUser($this->user);
        
        $startTime = microtime(true);
        $this->client->request('GET', '/investment/dashboard');
        $endTime = microtime(true);
        
        $responseTime = $endTime - $startTime;
        
        // Vérifier que la réponse est rapide (< 2 secondes)
        $this->assertLessThan(2, $responseTime);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testInvestmentListPerformance(): void
    {
        $this->client->loginUser($this->user);
        
        $startTime = microtime(true);
        $this->client->request('GET', '/investment/investments');
        $endTime = microtime(true);
        
        $responseTime = $endTime - $startTime;
        
        $this->assertLessThan(2, $responseTime);
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testInvestmentRoutesWithInvalidUser(): void
    {
        // Créer un utilisateur inactif
        $inactiveUser = new User();
        $inactiveUser->setEmail('inactive@example.com');
        $inactiveUser->setPseudo('inactive');
        $inactiveUser->setPassword('password123');
        $inactiveUser->setRoles(['ROLE_USER']);
        $inactiveUser->setIsActive(false);
        $inactiveUser->setIsVerified(false);
        
        $this->entityManager->persist($inactiveUser);
        $this->entityManager->flush();
        
        $this->client->loginUser($inactiveUser);
        
        // L'utilisateur inactif ne devrait pas pouvoir accéder
        $this->client->request('GET', '/investment/dashboard');
        
        // Devrait être redirigé ou refusé
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        
        // Nettoyer
        $this->entityManager->remove($inactiveUser);
        $this->entityManager->flush();
    }

    public function testInvestmentDashboardWithData(): void
    {
        $this->client->loginUser($this->user);
        
        // Le service devrait créer des données d'exemple automatiquement
        $crawler = $this->client->request('GET', '/investment/dashboard');
        
        // Vérifier que les données sont présentes
        $this->assertSelectorExists('.metric-value');
        $this->assertSelectorExists('.stat-number');
    }

    public function testInvestmentListWithData(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/investments');
        
        // Vérifier que la liste peut afficher des investissements
        $this->assertSelectorExists('#investmentsList');
    }

    public function testInvestmentCalendarWithData(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/calendar');
        
        // Vérifier que le calendrier peut afficher des données
        $this->assertSelectorExists('.calendar-month');
    }

    public function testInvestmentAssembliesWithData(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/assemblies');
        
        // Vérifier que les assemblées peuvent être affichées
        $this->assertSelectorExists('#assembliesList');
    }

    public function testInvestmentDocumentsWithData(): void
    {
        $this->client->loginUser($this->user);
        
        $crawler = $this->client->request('GET', '/investment/documents');
        
        // Vérifier que les documents peuvent être affichés
        $this->assertSelectorExists('#gridView');
        $this->assertSelectorExists('#listView');
    }
}
