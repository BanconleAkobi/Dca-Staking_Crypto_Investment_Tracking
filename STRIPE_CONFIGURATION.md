# 🔧 Configuration Stripe pour FolioZen

## 📋 Étapes pour configurer Stripe (Solution Simple)

### 1. Créer les liens de paiement dans Stripe Dashboard

1. **Connectez-vous** à votre [Stripe Dashboard](https://dashboard.stripe.com)
2. **Allez dans** "Paiements" → "Liens de paiement"
3. **Créez 2 liens de paiement** :

#### Lien Pro (9.99€/mois)
- **Nom** : FolioZen Pro
- **Prix** : 9.99€ par mois (récurrent)
- **Description** : Plan Pro avec fonctionnalités avancées
- **URL de succès** : `https://votre-domaine.com/billing/success`
- **URL d'annulation** : `https://votre-domaine.com/billing/cancel`
- **Copiez l'URL du lien** (commence par `https://buy.stripe.com/...`)

#### Lien Enterprise (29.99€/mois)
- **Nom** : FolioZen Enterprise  
- **Prix** : 29.99€ par mois (récurrent)
- **Description** : Plan Enterprise avec fonctionnalités illimitées
- **URL de succès** : `https://votre-domaine.com/billing/success`
- **URL d'annulation** : `https://votre-domaine.com/billing/cancel`
- **Copiez l'URL du lien** (commence par `https://buy.stripe.com/...`)

### 2. Mettre à jour la configuration

Remplacez les liens dans `config/services.yaml` :

```yaml
parameters:
    # Stripe Payment Links
    stripe.payment_link.pro: 'https://buy.stripe.com/VOTRE_LIEN_PRO_ICI'
    stripe.payment_link.enterprise: 'https://buy.stripe.com/VOTRE_LIEN_ENTERPRISE_ICI'
```

**Avantage** : Les liens sont maintenant centralisés dans la configuration et facilement modifiables !

### 3. Configuration des clés API et email

Ajoutez dans votre fichier `.env.local` :

```bash
# Clés Stripe (mode test)
STRIPE_PUBLIC_KEY=pk_test_votre_cle_publique_ici
STRIPE_SECRET_KEY=sk_test_votre_cle_privee_ici

# Configuration email (pour l'envoi de factures)
MAILER_DSN=smtp://localhost:1025
# Ou pour Gmail : smtp://username:password@smtp.gmail.com:587
# Ou pour SendGrid : smtp://apikey:YOUR_API_KEY@smtp.sendgrid.net:587

# Pour la production, utilisez les clés live :
# STRIPE_PUBLIC_KEY=pk_live_votre_cle_publique_ici
# STRIPE_SECRET_KEY=sk_live_votre_cle_privee_ici
```

### 4. Tester les paiements

1. **Mode test** : Utilisez les cartes de test Stripe
   - Succès : `4242 4242 4242 4242`
   - Échec : `4000 0000 0000 0002`

2. **Cliquez sur "S'abonner"** dans votre application
3. **Vérifiez** que vous êtes redirigé vers Stripe
4. **Après paiement** : Vérifiez que vous recevez les emails de facture et de bienvenue

## ✅ Avantages de cette solution

- **✅ Ultra simple** : Pas de produits à créer
- **✅ Pas de code complexe** : Redirection directe
- **✅ Gestion Stripe** : Tout est géré par Stripe
- **✅ Sécurisé** : Paiements sécurisés par Stripe
- **✅ Emails automatiques** : Facture et bienvenue envoyés
- **✅ Rapide à configurer** : 5 minutes max

## ⚠️ Important

- **Testez d'abord** en mode test avant de passer en production
- **Copiez les bons liens** dans le code
- **Vérifiez** que les redirections fonctionnent

## 🚨 Erreurs courantes

1. **"Lien de paiement non configuré"** → Vérifiez les `payment_link`
2. **"Page non trouvée"** → Vérifiez que les liens Stripe sont corrects
3. **"Redirection échouée"** → Vérifiez la syntaxe des URLs
