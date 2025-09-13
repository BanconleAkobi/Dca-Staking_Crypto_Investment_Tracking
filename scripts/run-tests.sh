#!/bin/bash

# Script pour exécuter tous les tests de l'application
# Usage: ./scripts/run-tests.sh [type] [options]

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_message() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Fonction pour afficher l'aide
show_help() {
    echo "Usage: $0 [TYPE] [OPTIONS]"
    echo ""
    echo "Types de tests disponibles:"
    echo "  unit          Tests unitaires"
    echo "  integration   Tests d'intégration"
    echo "  functional    Tests fonctionnels"
    echo "  e2e           Tests end-to-end"
    echo "  performance   Tests de performance"
    echo "  security      Tests de sécurité"
    echo "  all           Tous les tests (défaut)"
    echo ""
    echo "Options:"
    echo "  --coverage    Générer un rapport de couverture"
    echo "  --verbose     Mode verbeux"
    echo "  --stop-on-failure  Arrêter au premier échec"
    echo "  --help        Afficher cette aide"
    echo ""
    echo "Exemples:"
    echo "  $0 unit --coverage"
    echo "  $0 integration --verbose"
    echo "  $0 all --coverage --stop-on-failure"
}

# Variables par défaut
TEST_TYPE="all"
COVERAGE=false
VERBOSE=false
STOP_ON_FAILURE=false

# Parser les arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        unit|integration|functional|e2e|performance|security|all)
            TEST_TYPE="$1"
            shift
            ;;
        --coverage)
            COVERAGE=true
            shift
            ;;
        --verbose)
            VERBOSE=true
            shift
            ;;
        --stop-on-failure)
            STOP_ON_FAILURE=true
            shift
            ;;
        --help)
            show_help
            exit 0
            ;;
        *)
            print_error "Option inconnue: $1"
            show_help
            exit 1
            ;;
    esac
done

# Vérifier que PHPUnit est installé
if ! command -v php bin/console &> /dev/null; then
    print_error "Symfony CLI non trouvé. Assurez-vous d'être dans le répertoire du projet."
    exit 1
fi

# Vérifier que l'environnement de test est configuré
if [ ! -f ".env.test" ]; then
    print_warning "Fichier .env.test non trouvé. Création d'un fichier de test par défaut..."
    cat > .env.test << EOF
APP_ENV=test
APP_SECRET=test-secret-key
DATABASE_URL="sqlite:///:memory:"
STRIPE_PUBLIC_KEY=pk_test_fake
STRIPE_SECRET_KEY=sk_test_fake
STRIPE_WEBHOOK_SECRET=whsec_test_fake
EOF
fi

# Nettoyer le cache de test
print_message "Nettoyage du cache de test..."
php bin/console cache:clear --env=test --no-debug

# Créer le répertoire de couverture si nécessaire
if [ "$COVERAGE" = true ]; then
    mkdir -p var/coverage
    print_message "Rapport de couverture activé"
fi

# Construire la commande PHPUnit
PHPUNIT_CMD="php bin/phpunit"

if [ "$TEST_TYPE" != "all" ]; then
    PHPUNIT_CMD="$PHPUNIT_CMD --testsuite=$TEST_TYPE"
fi

if [ "$COVERAGE" = true ]; then
    PHPUNIT_CMD="$PHPUNIT_CMD --coverage-html=var/coverage/html --coverage-text"
fi

if [ "$VERBOSE" = true ]; then
    PHPUNIT_CMD="$PHPUNIT_CMD --verbose"
fi

if [ "$STOP_ON_FAILURE" = true ]; then
    PHPUNIT_CMD="$PHPUNIT_CMD --stop-on-failure"
fi

# Afficher les informations de test
print_message "Exécution des tests de type: $TEST_TYPE"
if [ "$COVERAGE" = true ]; then
    print_message "Rapport de couverture: var/coverage/html/index.html"
fi

# Exécuter les tests
print_message "Lancement des tests..."
echo "Commande: $PHPUNIT_CMD"
echo ""

# Exécuter la commande et capturer le code de sortie
if $PHPUNIT_CMD; then
    print_success "Tous les tests sont passés avec succès!"
    
    if [ "$COVERAGE" = true ]; then
        print_message "Rapport de couverture généré dans var/coverage/html/"
        if command -v open &> /dev/null; then
            print_message "Ouverture du rapport de couverture..."
            open var/coverage/html/index.html
        fi
    fi
    
    exit 0
else
    print_error "Certains tests ont échoué!"
    exit 1
fi
