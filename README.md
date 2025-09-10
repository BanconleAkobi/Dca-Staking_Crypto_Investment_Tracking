# 🚀 FolioZen - Plateforme de Gestion d'Investissements

**FolioZen** est une plateforme moderne et complète de gestion d'investissements qui permet de suivre et d'analyser tous types d'investissements en un seul endroit.

## ✨ Fonctionnalités

### 📊 Gestion Multi-Actifs
- **Cryptomonnaies** : Bitcoin, Ethereum, et toutes les cryptos
- **Actions** : Suivi des actions individuelles
- **ETF** : Fonds négociés en bourse
- **Obligations** : Titres de créance
- **Comptes d'épargne français** : Livret A, LDDS, LEP, PEL, etc.
- **Immobilier** : Suivi des investissements immobiliers
- **Matières premières** : Or, pétrole, etc.

### 💰 Suivi des Transactions
- Enregistrement des achats et ventes
- Calcul automatique des plus-values
- Historique complet des opérations
- Support multi-devises

### 📈 Analyses et Rapports
- Tableau de bord unifié
- Graphiques de performance
- Rapports PDF détaillés
- Export de données

### 🏦 Comptes d'Épargne Français
- **Livret A** : Plafond 22 950€, taux réglementé
- **LDDS** : Développement durable, plafond 12 000€
- **LEP** : Logement social, plafond 10 000€
- **PEL/CEL** : Épargne logement
- **Livret A Jeune** : Pour les 12-25 ans
- **Dépôts à terme** : Épargne bloquée
- **Assurance vie** : Épargne long terme

### 💸 Suivi des Retraits
- Catégorisation des retraits (retraite, urgence, rééquilibrage)
- Calcul des impôts et frais
- Historique des sorties d'argent

## 🎯 Plans d'Abonnement

### 🆓 Plan Gratuit
- 3 cryptomonnaies
- 5 actifs
- 3 comptes d'épargne
- 10 transactions
- 10 retraits
- Analyses de base

### ⭐ Plan Pro - 9,99€/mois
- 50 cryptomonnaies
- 100 actifs
- 20 comptes d'épargne
- 1000 transactions
- 500 retraits
- Rapports PDF
- Analyses avancées
- Export de données

### 🏢 Plan Entreprise - 29,99€/mois
- Actifs illimités
- Transactions illimitées
- Retraits illimités
- Rapports personnalisés
- Support dédié
- Intégrations personnalisées

## 🛠️ Technologies

- **Backend** : Symfony 7.x
- **Frontend** : Twig, Bootstrap 5, JavaScript
- **Base de données** : MySQL
- **Paiements** : Stripe
- **Design** : Interface moderne et responsive

## 🚀 Installation

### Prérequis
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js (pour les assets)

### Installation
```bash
# Cloner le projet
git clone https://github.com/votre-username/votre-projet.git
cd votre-projet

# Installer les dépendances PHP
composer install

# Installer les dépendances Node.js
npm install

# Configurer la base de données
# Créer un fichier .env avec vos paramètres
cp .env.example .env

# Créer la base de données
php bin/console doctrine:database:create

# Exécuter les migrations
php bin/console doctrine:migrations:migrate

# Charger les données de test (optionnel)
php bin/console doctrine:fixtures:load

# Compiler les assets
npm run build

# Lancer le serveur
symfony serve
```

### Configuration Stripe
1. Créez un compte sur [stripe.com](https://stripe.com)
2. Récupérez vos clés API
3. Configurez les variables d'environnement
4. Suivez le guide [STRIPE_SETUP.md](STRIPE_SETUP.md)

## 📁 Structure du Projet

```
src/
├── Controller/          # Contrôleurs Symfony
├── Entity/             # Entités Doctrine
├── Form/               # Formulaires Symfony
├── Repository/         # Repositories Doctrine
├── Service/            # Services métier
└── Twig/              # Extensions Twig

templates/
├── dashboard/          # Templates du tableau de bord
├── legal/             # Pages légales
├── asset/             # Gestion des actifs
├── savings_account/   # Comptes d'épargne
└── withdrawal/        # Retraits
```

## 🔒 Sécurité

- Authentification sécurisée
- Chiffrement des données sensibles
- Validation des entrées utilisateur
- Protection CSRF
- Conformité RGPD

## 📄 Pages Légales

- [Conditions Générales d'Utilisation](legal/terms)
- [Politique de Confidentialité](legal/privacy)
- [Avertissement et Prévention](legal/disclaimer)
- [Politique de Remboursement](legal/refund)
- [Contact et Support](legal/contact)

## 👨‍💼 Équipe

**CEO** : [Votre Nom]  
**Développement** : [Votre Équipe]  
**Support** : [votre-email@domaine.com]

## 📞 Contact

- **Email** : [votre-email@domaine.com]
- **Support** : [support@domaine.com]
- **Facturation** : [billing@domaine.com]
- **Légal** : [legal@domaine.com]

## 📜 Licence

© {{ "now"|date('Y') }} FolioZen. Tous droits réservés.

---

**FolioZen** - Votre partenaire pour une gestion d'investissements moderne et efficace.
