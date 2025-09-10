# Configuration Stripe pour InvestFlow

## 🔑 Configuration des clés Stripe

### 1. Créer un compte Stripe
1. Allez sur [stripe.com](https://stripe.com)
2. Créez un compte développeur
3. Activez votre compte avec vos informations bancaires

### 2. Récupérer vos clés API
1. Connectez-vous à votre [Dashboard Stripe](https://dashboard.stripe.com)
2. Allez dans **Développeurs > Clés API**
3. Copiez vos clés :
   - **Clé publique** (commence par `pk_test_` ou `pk_live_`)
   - **Clé secrète** (commence par `sk_test_` ou `sk_live_`)

### 3. Configurer les variables d'environnement
Créez un fichier `.env` à la racine du projet avec :

```env
# Stripe Configuration
STRIPE_PUBLIC_KEY=pk_test_votre_cle_publique_ici
STRIPE_SECRET_KEY=sk_test_votre_cle_secrete_ici
STRIPE_WEBHOOK_SECRET=whsec_votre_webhook_secret_ici
```

### 4. Créer les produits et prix dans Stripe
1. Allez dans **Produits** dans votre Dashboard Stripe
2. Créez les produits suivants :

#### Plan Pro (9,99€/mois)
- Nom : "InvestFlow Pro"
- Prix : 9,99€
- Intervalle : Mensuel
- Copiez l'ID du prix (commence par `price_`)

#### Plan Entreprise (29,99€/mois)
- Nom : "InvestFlow Entreprise"
- Prix : 29,99€
- Intervalle : Mensuel
- Copiez l'ID du prix (commence par `price_`)

### 5. Mettre à jour le service StripeService
Dans `src/Service/StripeService.php`, remplacez les IDs de prix :

```php
'stripe_price_id' => 'price_1234567890abcdef', // Votre ID Pro
'stripe_price_id' => 'price_0987654321fedcba', // Votre ID Entreprise
```

### 6. Configurer les webhooks (optionnel)
1. Allez dans **Développeurs > Webhooks**
2. Ajoutez un endpoint : `https://votre-domaine.com/stripe/webhook`
3. Sélectionnez les événements :
   - `checkout.session.completed`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
4. Copiez le secret du webhook

## 🚀 Test en mode développement

### Mode Test
- Utilisez les clés qui commencent par `pk_test_` et `sk_test_`
- Les paiements ne sont pas réels
- Utilisez les cartes de test Stripe

### Cartes de test Stripe
- **Succès** : 4242 4242 4242 4242
- **Échec** : 4000 0000 0000 0002
- **3D Secure** : 4000 0025 0000 3155

## 🔒 Sécurité

### Production
- Utilisez les clés qui commencent par `pk_live_` et `sk_live_`
- Activez le mode live dans votre Dashboard Stripe
- Configurez votre domaine de production

### Bonnes pratiques
- Ne jamais commiter les clés secrètes
- Utilisez des variables d'environnement
- Activez la validation des webhooks
- Surveillez les logs de paiement

## 📞 Support

Si vous avez des questions :
- Documentation Stripe : [stripe.com/docs](https://stripe.com/docs)
- Support InvestFlow : support@investflow.fr

---

**Note importante** : Les clés de test sont gratuites. Les cles de production nécessitent un compte Stripe vérifié.

