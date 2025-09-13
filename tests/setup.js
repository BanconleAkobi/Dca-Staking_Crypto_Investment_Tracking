// Configuration Jest pour les tests JavaScript
import '@testing-library/jest-dom';

// Mock des APIs du navigateur
global.fetch = jest.fn();

// Mock de Chart.js
jest.mock('chart.js', () => ({
  Chart: jest.fn().mockImplementation(() => ({
    destroy: jest.fn(),
    update: jest.fn(),
    resize: jest.fn(),
  })),
  registerables: [],
}));

// Mock de Bootstrap
jest.mock('bootstrap', () => ({
  Modal: jest.fn().mockImplementation(() => ({
    show: jest.fn(),
    hide: jest.fn(),
    dispose: jest.fn(),
  })),
  Tooltip: jest.fn().mockImplementation(() => ({
    show: jest.fn(),
    hide: jest.fn(),
    dispose: jest.fn(),
  })),
  Popover: jest.fn().mockImplementation(() => ({
    show: jest.fn(),
    hide: jest.fn(),
    dispose: jest.fn(),
  })),
}));

// Mock des événements personnalisés
global.CustomEvent = jest.fn();

// Mock de localStorage
const localStorageMock = {
  getItem: jest.fn(),
  setItem: jest.fn(),
  removeItem: jest.fn(),
  clear: jest.fn(),
};
global.localStorage = localStorageMock;

// Mock de sessionStorage
const sessionStorageMock = {
  getItem: jest.fn(),
  setItem: jest.fn(),
  removeItem: jest.fn(),
  clear: jest.fn(),
};
global.sessionStorage = sessionStorageMock;

// Mock de window.location
delete window.location;
window.location = {
  href: 'http://localhost:8000',
  pathname: '/',
  search: '',
  hash: '',
  assign: jest.fn(),
  replace: jest.fn(),
  reload: jest.fn(),
};

// Mock de window.history
window.history = {
  pushState: jest.fn(),
  replaceState: jest.fn(),
  back: jest.fn(),
  forward: jest.fn(),
  go: jest.fn(),
};

// Mock de window.alert
window.alert = jest.fn();

// Mock de window.confirm
window.confirm = jest.fn();

// Mock de window.open
window.open = jest.fn();

// Mock de console pour éviter les logs dans les tests
global.console = {
  ...console,
  log: jest.fn(),
  debug: jest.fn(),
  info: jest.fn(),
  warn: jest.fn(),
  error: jest.fn(),
};

// Configuration des tests
beforeEach(() => {
  // Nettoyer les mocks avant chaque test
  jest.clearAllMocks();
  
  // Réinitialiser localStorage
  localStorageMock.getItem.mockClear();
  localStorageMock.setItem.mockClear();
  localStorageMock.removeItem.mockClear();
  localStorageMock.clear.mockClear();
  
  // Réinitialiser sessionStorage
  sessionStorageMock.getItem.mockClear();
  sessionStorageMock.setItem.mockClear();
  sessionStorageMock.removeItem.mockClear();
  sessionStorageMock.clear.mockClear();
  
  // Réinitialiser fetch
  fetch.mockClear();
  
  // Réinitialiser les mocks de window
  window.alert.mockClear();
  window.confirm.mockClear();
  window.open.mockClear();
});

afterEach(() => {
  // Nettoyer le DOM après chaque test
  document.body.innerHTML = '';
});
