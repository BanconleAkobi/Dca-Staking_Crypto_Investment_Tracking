// Tests E2E pour le tableau de bord d'investissement
describe('Investment Dashboard', () => {
  beforeEach(() => {
    // Visiter la page de connexion
    cy.visit('/login');
    
    // Se connecter avec un utilisateur de test
    cy.get('input[name="email"]').type('test@example.com');
    cy.get('input[name="password"]').type('password123');
    cy.get('button[type="submit"]').click();
    
    // Attendre la redirection vers le tableau de bord
    cy.url().should('include', '/dashboard');
  });

  it('should display investment dashboard correctly', () => {
    // Naviguer vers le tableau de bord d'investissement
    cy.visit('/investment/dashboard');
    
    // Vérifier le titre de la page
    cy.contains('h1', 'Tableau de Bord Investissement').should('be.visible');
    
    // Vérifier la présence des métriques principales
    cy.get('.metric-card').should('have.length.at.least', 4);
    cy.contains('.metric-label', 'Investissement Total').should('be.visible');
    cy.contains('.metric-label', 'Valeur Actuelle').should('be.visible');
    cy.contains('.metric-label', 'Gain/Perte').should('be.visible');
    cy.contains('.metric-label', 'Performance').should('be.visible');
  });

  it('should display portfolio statistics', () => {
    cy.visit('/investment/dashboard');
    
    // Vérifier les statistiques du portefeuille
    cy.get('.stat-card').should('have.length.at.least', 4);
    cy.contains('.stat-label', 'Investissements Actifs').should('be.visible');
    cy.contains('.stat-label', 'Investissements Terminés').should('be.visible');
    cy.contains('.stat-label', 'Paiements à Venir').should('be.visible');
    cy.contains('.stat-label', 'Entreprises en Bonne Santé').should('be.visible');
  });

  it('should display charts correctly', () => {
    cy.visit('/investment/dashboard');
    
    // Vérifier la présence des graphiques
    cy.get('#monthlyEvolutionChart').should('be.visible');
    cy.get('#sectorChart').should('be.visible');
    cy.get('#assetTypeChart').should('be.visible');
    
    // Vérifier que les graphiques sont des canvas
    cy.get('#monthlyEvolutionChart').should('have.prop', 'tagName', 'CANVAS');
    cy.get('#sectorChart').should('have.prop', 'tagName', 'CANVAS');
    cy.get('#assetTypeChart').should('have.prop', 'tagName', 'CANVAS');
  });

  it('should display upcoming payments', () => {
    cy.visit('/investment/dashboard');
    
    // Vérifier la section des paiements à venir
    cy.contains('h5', 'Paiements à Venir').should('be.visible');
    
    // Vérifier la présence des paiements (même s'ils sont vides)
    cy.get('.payment-item').should('exist');
  });

  it('should have working quick action buttons', () => {
    cy.visit('/investment/dashboard');
    
    // Vérifier les boutons d'actions rapides
    cy.contains('a', 'Mes Investissements').should('be.visible');
    cy.contains('a', 'Calendrier des Échéances').should('be.visible');
    cy.contains('a', 'Assemblées Générales').should('be.visible');
    cy.contains('a', 'Documents').should('be.visible');
    
    // Tester la navigation vers les investissements
    cy.contains('a', 'Mes Investissements').click();
    cy.url().should('include', '/investment/investments');
    
    // Retourner au tableau de bord
    cy.visit('/investment/dashboard');
    
    // Tester la navigation vers le calendrier
    cy.contains('a', 'Calendrier des Échéances').click();
    cy.url().should('include', '/investment/calendar');
  });

  it('should be responsive on mobile devices', () => {
    // Tester sur mobile
    cy.viewport('iphone-x');
    cy.visit('/investment/dashboard');
    
    // Vérifier que le contenu est visible sur mobile
    cy.contains('h1', 'Tableau de Bord Investissement').should('be.visible');
    cy.get('.metric-card').should('be.visible');
    
    // Vérifier que le menu mobile est présent
    cy.get('.mobile-menu-toggle').should('be.visible');
  });

  it('should handle theme switching', () => {
    cy.visit('/investment/dashboard');
    
    // Vérifier le bouton de changement de thème
    cy.get('.theme-toggle').should('be.visible');
    
    // Cliquer sur le bouton de thème
    cy.get('.theme-toggle').click();
    
    // Vérifier que le thème sombre est appliqué
    cy.get('html').should('have.attr', 'data-theme', 'dark');
    
    // Cliquer à nouveau pour revenir au thème clair
    cy.get('.theme-toggle').click();
    cy.get('html').should('not.have.attr', 'data-theme');
  });

  it('should load data correctly', () => {
    cy.visit('/investment/dashboard');
    
    // Vérifier que les données sont chargées
    cy.get('.metric-value').should('be.visible');
    cy.get('.stat-number').should('be.visible');
    
    // Vérifier que les valeurs ne sont pas vides
    cy.get('.metric-value').should('not.be.empty');
    cy.get('.stat-number').should('not.be.empty');
  });

  it('should handle errors gracefully', () => {
    // Intercepter les requêtes API pour simuler une erreur
    cy.intercept('GET', '/api/investment/dashboard', { statusCode: 500 }).as('dashboardError');
    
    cy.visit('/investment/dashboard');
    
    // Vérifier que l'erreur est gérée gracieusement
    cy.wait('@dashboardError');
    
    // La page devrait toujours être accessible
    cy.contains('h1', 'Tableau de Bord Investissement').should('be.visible');
  });

  it('should have proper accessibility', () => {
    cy.visit('/investment/dashboard');
    
    // Vérifier les attributs d'accessibilité
    cy.get('.metric-card').should('have.attr', 'role', 'region');
    cy.get('.stat-card').should('have.attr', 'role', 'region');
    
    // Vérifier que les boutons ont des labels appropriés
    cy.get('.theme-toggle').should('have.attr', 'aria-label');
    
    // Vérifier la navigation au clavier
    cy.get('.theme-toggle').focus().should('be.focused');
  });

  it('should perform well under load', () => {
    const startTime = Date.now();
    
    cy.visit('/investment/dashboard');
    
    // Vérifier que la page se charge rapidement
    cy.get('.metric-card').should('be.visible').then(() => {
      const loadTime = Date.now() - startTime;
      expect(loadTime).to.be.lessThan(3000); // Moins de 3 secondes
    });
  });
});
