# Script PowerShell pour exécuter tous les tests de l'application
# Usage: .\scripts\run-tests.ps1 [type] [options]

param(
    [Parameter(Position=0)]
    [ValidateSet("unit", "integration", "functional", "e2e", "performance", "security", "all")]
    [string]$TestType = "all",
    
    [switch]$Coverage,
    [switch]$Verbose,
    [switch]$StopOnFailure,
    [switch]$Help
)

# Fonction pour afficher les messages
function Write-Info {
    param([string]$Message)
    Write-Host "[INFO] $Message" -ForegroundColor Blue
}

function Write-Success {
    param([string]$Message)
    Write-Host "[SUCCESS] $Message" -ForegroundColor Green
}

function Write-Warning {
    param([string]$Message)
    Write-Host "[WARNING] $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "[ERROR] $Message" -ForegroundColor Red
}

# Fonction pour afficher l'aide
function Show-Help {
    Write-Host "Usage: .\scripts\run-tests.ps1 [TYPE] [OPTIONS]" -ForegroundColor White
    Write-Host ""
    Write-Host "Types de tests disponibles:" -ForegroundColor White
    Write-Host "  unit          Tests unitaires" -ForegroundColor Gray
    Write-Host "  integration   Tests d'intégration" -ForegroundColor Gray
    Write-Host "  functional    Tests fonctionnels" -ForegroundColor Gray
    Write-Host "  e2e           Tests end-to-end" -ForegroundColor Gray
    Write-Host "  performance   Tests de performance" -ForegroundColor Gray
    Write-Host "  security      Tests de sécurité" -ForegroundColor Gray
    Write-Host "  all           Tous les tests (défaut)" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Options:" -ForegroundColor White
    Write-Host "  -Coverage     Générer un rapport de couverture" -ForegroundColor Gray
    Write-Host "  -Verbose      Mode verbeux" -ForegroundColor Gray
    Write-Host "  -StopOnFailure Arrêter au premier échec" -ForegroundColor Gray
    Write-Host "  -Help         Afficher cette aide" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Exemples:" -ForegroundColor White
    Write-Host "  .\scripts\run-tests.ps1 unit -Coverage" -ForegroundColor Gray
    Write-Host "  .\scripts\run-tests.ps1 integration -Verbose" -ForegroundColor Gray
    Write-Host "  .\scripts\run-tests.ps1 all -Coverage -StopOnFailure" -ForegroundColor Gray
}

# Afficher l'aide si demandé
if ($Help) {
    Show-Help
    exit 0
}

# Vérifier que nous sommes dans le bon répertoire
if (-not (Test-Path "composer.json")) {
    Write-Error "Fichier composer.json non trouvé. Assurez-vous d'être dans le répertoire du projet."
    exit 1
}

# Vérifier que l'environnement de test est configuré
if (-not (Test-Path ".env.test")) {
    Write-Warning "Fichier .env.test non trouvé. Création d'un fichier de test par défaut..."
    @"
APP_ENV=test
APP_SECRET=test-secret-key
DATABASE_URL="sqlite:///:memory:"
STRIPE_PUBLIC_KEY=pk_test_fake
STRIPE_SECRET_KEY=sk_test_fake
STRIPE_WEBHOOK_SECRET=whsec_test_fake
"@ | Out-File -FilePath ".env.test" -Encoding UTF8
}

# Nettoyer le cache de test
Write-Info "Nettoyage du cache de test..."
php bin/console cache:clear --env=test --no-debug

# Créer le répertoire de couverture si nécessaire
if ($Coverage) {
    if (-not (Test-Path "var/coverage")) {
        New-Item -ItemType Directory -Path "var/coverage" -Force | Out-Null
    }
    Write-Info "Rapport de couverture activé"
}

# Construire la commande PHPUnit
$phpunitCmd = "php bin/phpunit"

if ($TestType -ne "all") {
    $phpunitCmd += " --testsuite=$TestType"
}

if ($Coverage) {
    $phpunitCmd += " --coverage-html=var/coverage/html --coverage-text"
}

if ($Verbose) {
    $phpunitCmd += " --verbose"
}

if ($StopOnFailure) {
    $phpunitCmd += " --stop-on-failure"
}

# Afficher les informations de test
Write-Info "Exécution des tests de type: $TestType"
if ($Coverage) {
    Write-Info "Rapport de couverture: var/coverage/html/index.html"
}

# Exécuter les tests
Write-Info "Lancement des tests..."
Write-Host "Commande: $phpunitCmd" -ForegroundColor Gray
Write-Host ""

# Exécuter la commande et capturer le code de sortie
try {
    Invoke-Expression $phpunitCmd
    $exitCode = $LASTEXITCODE
    
    if ($exitCode -eq 0) {
        Write-Success "Tous les tests sont passés avec succès!"
        
        if ($Coverage) {
            Write-Info "Rapport de couverture généré dans var/coverage/html/"
            if (Test-Path "var/coverage/html/index.html") {
                Write-Info "Ouverture du rapport de couverture..."
                Start-Process "var/coverage/html/index.html"
            }
        }
        
        exit 0
    } else {
        Write-Error "Certains tests ont échoué!"
        exit $exitCode
    }
} catch {
    Write-Error "Erreur lors de l'exécution des tests: $_"
    exit 1
}
