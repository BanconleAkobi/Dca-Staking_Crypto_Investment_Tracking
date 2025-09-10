# 🎨 Guide de Personnalisation

Ce guide vous explique comment personnaliser votre plateforme de gestion d'investissements avant la mise en production.

## 🏷️ Personnalisation du Nom

### 1. Remplacez les placeholders
Recherchez et remplacez dans tous les fichiers :
- `FolioZen` → Votre nom de plateforme (si vous voulez changer)
- `[Votre Société]` → Votre nom de société
- `[Votre Nom]` → Votre nom en tant que CEO
- `[votre-email@domaine.com]` → Vos emails de contact

### 2. Fichiers à modifier
- `README.md`
- `templates/dashboard/layout.html.twig`
- `templates/dashboard/index.html.twig`
- `templates/legal/*.html.twig`
- `src/Controller/LegalController.php`

## 🌐 Configuration des Emails

### 1. Emails de contact
Dans `templates/legal/contact.html.twig` et `templates/legal/terms.html.twig` :
```twig
support@votre-domaine.com
billing@votre-domaine.com
legal@votre-domaine.com
contact@votre-domaine.com
```

### 2. Configuration SMTP
Dans votre fichier `.env` :
```env
MAILER_DSN=smtp://username:password@smtp.votre-domaine.com:587
```

## 🎨 Personnalisation Visuelle

### 1. Couleurs principales
Dans `public/styles/dashboard.css`, modifiez les variables CSS :
```css
:root {
    --primary-color: #667eea;    /* Votre couleur principale */
    --secondary-color: #764ba2;  /* Votre couleur secondaire */
    --success-color: #28a745;    /* Couleur de succès */
    --warning-color: #ffc107;    /* Couleur d'avertissement */
    --danger-color: #dc3545;     /* Couleur de danger */
}
```

### 2. Logo
Remplacez l'icône dans le footer :
```twig
<i class="fas fa-chart-line me-2"></i>
```
Par votre logo ou une icône personnalisée.

## 🔧 Configuration Stripe

### 1. Créer les produits
Dans votre Dashboard Stripe :
- **Plan Pro** : 9,99€/mois
- **Plan Entreprise** : 29,99€/mois

### 2. Mettre à jour les IDs
Dans `src/Service/StripeService.php` :
```php
'stripe_price_id' => 'price_votre_id_pro',
'stripe_price_id' => 'price_votre_id_enterprise',
```

## 🗄️ Base de Données

### 1. Nom de la base
Dans votre `.env` :
```env
DATABASE_URL="mysql://username:password@localhost:3306/votre_nom_db?serverVersion=8.0.32&charset=utf8mb4"
```

### 2. Migrations
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

## 🚀 Mise en Production

### 1. Environnement
```env
APP_ENV=prod
APP_SECRET=votre_secret_production
```

### 2. Optimisations
```bash
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console assets:install --env=prod
```

### 3. Hostinger
- Upload des fichiers via FTP/SFTP
- Configuration de la base de données MySQL
- Configuration du domaine
- SSL/HTTPS

## 📋 Checklist de Mise en Production

- [ ] Nom de la plateforme personnalisé
- [ ] Emails de contact configurés
- [ ] Couleurs personnalisées
- [ ] Logo ajouté
- [ ] Stripe configuré avec les vrais IDs
- [ ] Base de données créée
- [ ] Variables d'environnement de production
- [ ] SSL/HTTPS activé
- [ ] Tests de paiement effectués
- [ ] Pages légales vérifiées

## 🔒 Sécurité

### 1. Variables sensibles
Ne jamais commiter :
- `.env`
- Clés API Stripe
- Mots de passe de base de données
- Secrets d'application

### 2. Permissions
```bash
chmod 755 var/
chmod 755 public/
```

## 📞 Support

Pour toute question sur la personnalisation :
- Consultez la documentation Symfony
- Guide Stripe : [stripe.com/docs](https://stripe.com/docs)
- Documentation Hostinger

---

**Bon déploiement !** 🚀
