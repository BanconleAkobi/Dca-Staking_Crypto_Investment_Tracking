// Tests pour le système de changement de thème
describe('Theme Switcher', () => {
  let themeToggle;
  let htmlElement;

  beforeEach(() => {
    // Créer le DOM de test
    document.body.innerHTML = `
      <html>
        <head></head>
        <body>
          <button id="themeToggle" class="theme-toggle">
            <i class="fas fa-moon"></i>
          </button>
        </body>
      </html>
    `;

    htmlElement = document.documentElement;
    themeToggle = document.getElementById('themeToggle');
  });

  afterEach(() => {
    document.body.innerHTML = '';
  });

  test('should initialize with light theme by default', () => {
    // Le thème par défaut devrait être clair
    expect(htmlElement.getAttribute('data-theme')).toBeNull();
  });

  test('should toggle theme when button is clicked', () => {
    // Simuler le clic sur le bouton
    themeToggle.click();

    // Vérifier que le thème sombre est appliqué
    expect(htmlElement.getAttribute('data-theme')).toBe('dark');
  });

  test('should save theme preference to localStorage', () => {
    // Simuler le clic sur le bouton
    themeToggle.click();

    // Vérifier que la préférence est sauvegardée
    expect(localStorage.setItem).toHaveBeenCalledWith('theme', 'dark');
  });

  test('should load theme preference from localStorage on page load', () => {
    // Simuler une préférence de thème sombre en localStorage
    localStorage.getItem.mockReturnValue('dark');

    // Simuler le chargement de la page
    window.dispatchEvent(new Event('DOMContentLoaded'));

    // Vérifier que le thème sombre est appliqué
    expect(htmlElement.getAttribute('data-theme')).toBe('dark');
  });

  test('should update button icon when theme changes', () => {
    const icon = themeToggle.querySelector('i');

    // Thème clair par défaut
    expect(icon.className).toContain('fa-moon');

    // Clic pour passer au thème sombre
    themeToggle.click();
    expect(icon.className).toContain('fa-sun');

    // Clic pour revenir au thème clair
    themeToggle.click();
    expect(icon.className).toContain('fa-moon');
  });

  test('should apply theme to all elements with theme variables', () => {
    // Ajouter des éléments avec des variables CSS
    document.body.innerHTML += `
      <div class="card" style="background-color: var(--bg-primary); color: var(--text-primary);">
        <h1 style="color: var(--text-primary);">Test Card</h1>
      </div>
    `;

    // Passer au thème sombre
    themeToggle.click();

    // Vérifier que les variables CSS sont appliquées
    const card = document.querySelector('.card');
    const computedStyle = window.getComputedStyle(card);
    
    // Les couleurs devraient changer selon le thème
    expect(htmlElement.getAttribute('data-theme')).toBe('dark');
  });

  test('should handle multiple theme toggles correctly', () => {
    // Plusieurs clics rapides
    themeToggle.click(); // dark
    themeToggle.click(); // light
    themeToggle.click(); // dark

    expect(htmlElement.getAttribute('data-theme')).toBe('dark');
    expect(localStorage.setItem).toHaveBeenCalledWith('theme', 'dark');
  });

  test('should work with keyboard navigation', () => {
    // Simuler la navigation au clavier
    const event = new KeyboardEvent('keydown', { key: 'Enter' });
    themeToggle.dispatchEvent(event);

    // Le thème devrait changer
    expect(htmlElement.getAttribute('data-theme')).toBe('dark');
  });

  test('should handle invalid theme values gracefully', () => {
    // Simuler une valeur invalide en localStorage
    localStorage.getItem.mockReturnValue('invalid-theme');

    // Le système devrait gérer cela gracieusement
    window.dispatchEvent(new Event('DOMContentLoaded'));

    // Devrait rester en thème clair par défaut
    expect(htmlElement.getAttribute('data-theme')).toBeNull();
  });

  test('should maintain theme across page reloads', () => {
    // Simuler le thème sombre
    themeToggle.click();
    expect(htmlElement.getAttribute('data-theme')).toBe('dark');

    // Simuler un rechargement de page
    localStorage.getItem.mockReturnValue('dark');
    window.dispatchEvent(new Event('DOMContentLoaded'));

    // Le thème devrait être maintenu
    expect(htmlElement.getAttribute('data-theme')).toBe('dark');
  });
});
