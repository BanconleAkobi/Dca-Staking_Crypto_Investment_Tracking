# 🧪 Guide de Tests Humains - FolioZen

## 📋 Vue d'ensemble
Ce document contient tous les tests manuels à effectuer pour valider le bon fonctionnement de la plateforme FolioZen. Chaque test doit être exécuté et les résultats documentés.

---

## 🎯 Tests d'Authentification

### 1. Page d'Accueil et Navigation
- [ ] **Test 1.1** : Accéder à la page d'accueil
  - **Action** : Ouvrir `http://localhost:8000`
  - **Résultat attendu** : Page d'accueil avec branding FolioZen, navbar moderne
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 1.2** : Navigation vers la page de connexion
  - **Action** : Cliquer sur "Connexion" dans la navbar
  - **Résultat attendu** : Page de connexion avec design moderne, branding FolioZen
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 1.3** : Navigation vers la page d'inscription
  - **Action** : Cliquer sur "S'inscrire" dans la navbar
  - **Résultat attendu** : Page d'inscription avec design moderne, branding FolioZen
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 2. Inscription Utilisateur
- [ ] **Test 2.1** : Inscription avec données valides
  - **Action** : Remplir le formulaire d'inscription avec :
    - Email : `test@example.com`
    - Pseudo : `testuser`
    - Mot de passe : `TestPassword123!`
    - Confirmation : `TestPassword123!`
  - **Résultat attendu** : Inscription réussie, redirection vers dashboard
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 2.2** : Inscription avec email existant
  - **Action** : Tenter de s'inscrire avec un email déjà utilisé
  - **Résultat attendu** : Message d'erreur "Email déjà utilisé"
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 2.3** : Inscription avec mot de passe faible
  - **Action** : Tenter de s'inscrire avec mot de passe "123"
  - **Résultat attendu** : Message d'erreur de validation
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 3. Connexion Utilisateur
- [ ] **Test 3.1** : Connexion avec identifiants valides
  - **Action** : Se connecter avec les identifiants créés
  - **Résultat attendu** : Connexion réussie, redirection vers dashboard
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 3.2** : Connexion avec identifiants invalides
  - **Action** : Tenter de se connecter avec mauvais mot de passe
  - **Résultat attendu** : Message d'erreur "Identifiants invalides"
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 4. Réinitialisation de Mot de Passe
- [ ] **Test 4.1** : Demande de réinitialisation
  - **Action** : Cliquer sur "Mot de passe oublié" et saisir email
  - **Résultat attendu** : Message de confirmation d'envoi d'email
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 4.2** : Réinitialisation avec lien valide
  - **Action** : Cliquer sur le lien dans l'email (simulation)
  - **Résultat attendu** : Page de nouveau mot de passe
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 🏠 Tests du Dashboard Principal

### 5. Interface Dashboard
- [ ] **Test 5.1** : Affichage du dashboard après connexion
  - **Action** : Se connecter et accéder au dashboard
  - **Résultat attendu** : Dashboard avec sidebar, header, contenu principal
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 5.2** : Navigation sidebar
  - **Action** : Cliquer sur chaque élément du menu sidebar
  - **Résultat attendu** : Navigation fluide vers chaque section
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 5.3** : Toggle thème sombre/clair
  - **Action** : Cliquer sur le toggle de thème dans le header
  - **Résultat attendu** : Changement de thème immédiat
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 5.4** : Menu mobile
  - **Action** : Réduire la fenêtre et tester le menu hamburger
  - **Résultat attendu** : Menu mobile fonctionnel avec overlay
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 6. Statistiques Dashboard
- [ ] **Test 6.1** : Affichage des cartes statistiques
  - **Action** : Vérifier l'affichage des cartes de stats
  - **Résultat attendu** : Cartes avec icônes, valeurs, et couleurs appropriées
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 6.2** : Graphiques interactifs
  - **Action** : Vérifier les graphiques Chart.js
  - **Résultat attendu** : Graphiques s'affichent correctement
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 💰 Tests de Gestion des Actifs

### 7. Gestion des Actifs
- [ ] **Test 7.1** : Affichage de la liste des actifs
  - **Action** : Naviguer vers "Actifs" dans le sidebar
  - **Résultat attendu** : Liste des actifs avec filtres par type
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 7.2** : Ajout d'un nouvel actif
  - **Action** : Cliquer sur "Ajouter un actif" et remplir le formulaire
  - **Résultat attendu** : Actif créé avec succès, redirection vers la liste
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 7.3** : Modification d'un actif
  - **Action** : Cliquer sur "Modifier" sur un actif existant
  - **Résultat attendu** : Formulaire pré-rempli, modification sauvegardée
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 7.4** : Suppression d'un actif
  - **Action** : Tenter de supprimer un actif
  - **Résultat attendu** : Confirmation, suppression si pas de transactions
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 7.5** : Activation/Désactivation d'actif
  - **Action** : Toggle le statut d'un actif
  - **Résultat attendu** : Statut changé, message de confirmation
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 8. Gestion des Cryptomonnaies
- [ ] **Test 8.1** : Affichage des cryptos
  - **Action** : Naviguer vers "Cryptomonnaies"
  - **Résultat attendu** : Liste des cryptos avec prix en temps réel
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 8.2** : Ajout d'une crypto
  - **Action** : Ajouter une nouvelle cryptomonnaie
  - **Résultat attendu** : Crypto ajoutée avec données API
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 9. Gestion des Comptes d'Épargne
- [ ] **Test 9.1** : Affichage des comptes d'épargne
  - **Action** : Naviguer vers "Épargne"
  - **Résultat attendu** : Liste des comptes d'épargne
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 9.2** : Ajout d'un compte d'épargne
  - **Action** : Créer un nouveau compte d'épargne
  - **Résultat attendu** : Compte créé avec taux d'intérêt
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 📊 Tests des Transactions

### 10. Gestion des Transactions
- [ ] **Test 10.1** : Affichage des transactions
  - **Action** : Naviguer vers "Transactions"
  - **Résultat attendu** : Liste des transactions avec filtres
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 10.2** : Ajout d'une transaction d'achat
  - **Action** : Créer une transaction d'achat de crypto
  - **Résultat attendu** : Transaction enregistrée, portefeuille mis à jour
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 10.3** : Ajout d'une transaction de vente
  - **Action** : Créer une transaction de vente
  - **Résultat attendu** : Transaction enregistrée, quantité mise à jour
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 10.4** : Ajout d'une récompense de staking
  - **Action** : Enregistrer une récompense de staking
  - **Résultat attendu** : Récompense ajoutée sans coût d'investissement
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 10.5** : Modification d'une transaction
  - **Action** : Modifier une transaction existante
  - **Résultat attendu** : Modification sauvegardée, calculs mis à jour
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 10.6** : Suppression d'une transaction
  - **Action** : Supprimer une transaction
  - **Résultat attendu** : Transaction supprimée, portefeuille recalculé
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 11. Gestion des Retraits
- [ ] **Test 11.1** : Affichage des retraits
  - **Action** : Naviguer vers "Retraits"
  - **Résultat attendu** : Liste des retraits avec statuts
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 11.2** : Demande de retrait
  - **Action** : Créer une demande de retrait
  - **Résultat attendu** : Demande créée avec statut "En attente"
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 📈 Tests d'Analyses et Rapports

### 12. Dashboard d'Investissement
- [ ] **Test 12.1** : Affichage du dashboard d'investissement
  - **Action** : Naviguer vers "Investissements" dans Analyses
  - **Résultat attendu** : Dashboard avec métriques avancées
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 12.2** : Graphiques de performance
  - **Action** : Vérifier les graphiques de performance
  - **Résultat attendu** : Graphiques interactifs avec données réelles
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 13. Analyses Avancées
- [ ] **Test 13.1** : Page d'analyses
  - **Action** : Naviguer vers "Analyses"
  - **Résultat attendu** : Analyses détaillées du portefeuille
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 13.2** : Distribution par secteur
  - **Action** : Vérifier la distribution des investissements
  - **Résultat attendu** : Graphiques de distribution corrects
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 14. Génération de Rapports PDF
- [ ] **Test 14.1** : Génération de rapport PDF
  - **Action** : Cliquer sur "Rapport PDF" dans le dashboard
  - **Résultat attendu** : PDF généré et téléchargé
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 14.2** : Contenu du rapport PDF
  - **Action** : Vérifier le contenu du PDF généré
  - **Résultat attendu** : Rapport complet avec graphiques et données
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 🏛️ Tests des Fonctionnalités Avancées

### 15. Calendrier des Échéances
- [ ] **Test 15.1** : Affichage du calendrier
  - **Action** : Naviguer vers "Calendrier"
  - **Résultat attendu** : Calendrier avec échéances d'investissements
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 15.2** : Ajout d'échéance
  - **Action** : Ajouter une nouvelle échéance
  - **Résultat attendu** : Échéance ajoutée au calendrier
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 16. Assemblées Générales
- [ ] **Test 16.1** : Affichage des assemblées
  - **Action** : Naviguer vers "Assemblées"
  - **Résultat attendu** : Liste des assemblées avec dates de vote
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 16.2** : Participation au vote
  - **Action** : Voter sur une assemblée
  - **Résultat attendu** : Vote enregistré avec confirmation
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 17. Gestion des Documents
- [ ] **Test 17.1** : Affichage des documents
  - **Action** : Naviguer vers "Documents"
  - **Résultat attendu** : Liste des documents avec catégories
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 17.2** : Upload de document
  - **Action** : Télécharger un nouveau document
  - **Résultat attendu** : Document uploadé et catégorisé
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 💳 Tests de Facturation

### 18. Gestion des Abonnements
- [ ] **Test 18.1** : Affichage de la facturation
  - **Action** : Naviguer vers "Facturation"
  - **Résultat attendu** : Page de facturation avec plan actuel
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 18.2** : Changement de plan
  - **Action** : Tenter de changer de plan d'abonnement
  - **Résultat attendu** : Redirection vers Stripe ou message approprié
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 18.3** : Historique des factures
  - **Action** : Vérifier l'historique des factures
  - **Résultat attendu** : Liste des factures avec statuts
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 🔔 Tests de Communication

### 19. Notifications
- [ ] **Test 19.1** : Affichage des notifications
  - **Action** : Naviguer vers "Notifications"
  - **Résultat attendu** : Liste des notifications avec types
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 19.2** : Marquer comme lu
  - **Action** : Marquer une notification comme lue
  - **Résultat attendu** : Notification marquée, compteur mis à jour
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 19.3** : Suppression de notification
  - **Action** : Supprimer une notification
  - **Résultat attendu** : Notification supprimée de la liste
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## ⚙️ Tests des Paramètres

### 20. Paramètres Utilisateur
- [ ] **Test 20.1** : Affichage des paramètres
  - **Action** : Naviguer vers "Paramètres"
  - **Résultat attendu** : Page de paramètres avec sections
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 20.2** : Modification du profil
  - **Action** : Modifier les informations du profil
  - **Résultat attendu** : Modifications sauvegardées
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 20.3** : Changement de mot de passe
  - **Action** : Changer le mot de passe
  - **Résultat attendu** : Mot de passe changé avec confirmation
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 20.4** : Préférences de thème
  - **Action** : Changer les préférences de thème
  - **Résultat attendu** : Thème appliqué et sauvegardé
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 📱 Tests de Responsivité

### 21. Tests Mobile
- [ ] **Test 21.1** : Affichage mobile du dashboard
  - **Action** : Ouvrir le site sur mobile ou réduire la fenêtre
  - **Résultat attendu** : Interface adaptée, menu hamburger fonctionnel
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 21.2** : Navigation mobile
  - **Action** : Tester la navigation sur mobile
  - **Résultat attendu** : Navigation fluide, sidebar mobile
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 21.3** : Formulaires mobiles
  - **Action** : Tester les formulaires sur mobile
  - **Résultat attendu** : Formulaires adaptés, clavier virtuel
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 22. Tests Tablette
- [ ] **Test 22.1** : Affichage tablette
  - **Action** : Tester sur une résolution tablette
  - **Résultat attendu** : Interface adaptée pour tablette
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 🔒 Tests de Sécurité

### 23. Tests d'Accès
- [ ] **Test 23.1** : Accès non autorisé
  - **Action** : Tenter d'accéder à une page protégée sans être connecté
  - **Résultat attendu** : Redirection vers la page de connexion
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 23.2** : Accès aux données d'un autre utilisateur
  - **Action** : Tenter d'accéder aux données d'un autre utilisateur
  - **Résultat attendu** : Accès refusé ou données filtrées
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 23.3** : Protection CSRF
  - **Action** : Tenter une action sans token CSRF
  - **Résultat attendu** : Erreur CSRF ou action refusée
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 24. Tests de Validation
- [ ] **Test 24.1** : Injection SQL
  - **Action** : Tenter des injections SQL dans les formulaires
  - **Résultat attendu** : Données échappées, pas d'injection
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 24.2** : XSS
  - **Action** : Tenter des scripts XSS dans les champs
  - **Résultat attendu** : Scripts échappés, pas d'exécution
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## ⚡ Tests de Performance

### 25. Tests de Chargement
- [ ] **Test 25.1** : Temps de chargement initial
  - **Action** : Mesurer le temps de chargement de la page d'accueil
  - **Résultat attendu** : < 3 secondes
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 25.2** : Temps de chargement du dashboard
  - **Action** : Mesurer le temps de chargement du dashboard
  - **Résultat attendu** : < 5 secondes
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 25.3** : Chargement des graphiques
  - **Action** : Vérifier le temps de chargement des graphiques
  - **Résultat attendu** : Graphiques chargés rapidement
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

### 26. Tests de Mémoire
- [ ] **Test 26.1** : Utilisation mémoire
  - **Action** : Vérifier l'utilisation mémoire avec plusieurs onglets
  - **Résultat attendu** : Pas de fuite mémoire importante
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 🌐 Tests de Compatibilité

### 27. Tests Navigateurs
- [ ] **Test 27.1** : Chrome
  - **Action** : Tester toutes les fonctionnalités sur Chrome
  - **Résultat attendu** : Fonctionnement complet
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 27.2** : Firefox
  - **Action** : Tester toutes les fonctionnalités sur Firefox
  - **Résultat attendu** : Fonctionnement complet
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 27.3** : Safari
  - **Action** : Tester toutes les fonctionnalités sur Safari
  - **Résultat attendu** : Fonctionnement complet
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

- [ ] **Test 27.4** : Edge
  - **Action** : Tester toutes les fonctionnalités sur Edge
  - **Résultat attendu** : Fonctionnement complet
  - **Résultat obtenu** : ________________
  - **Status** : ✅ Pass / ❌ Fail

---

## 📊 Résumé des Tests

### Statistiques Globales
- **Total des tests** : 100+
- **Tests passés** : ___ / ___
- **Tests échoués** : ___ / ___
- **Taux de réussite** : ___%

### Tests Critiques (Doivent tous passer)
- [ ] Authentification complète
- [ ] Dashboard principal
- [ ] Gestion des actifs
- [ ] Gestion des transactions
- [ ] Génération de rapports PDF
- [ ] Responsivité mobile
- [ ] Sécurité de base

### Tests de Performance
- [ ] Temps de chargement < 3s
- [ ] Pas de fuite mémoire
- [ ] Graphiques fluides

### Tests de Compatibilité
- [ ] Chrome ✅ / ❌
- [ ] Firefox ✅ / ❌
- [ ] Safari ✅ / ❌
- [ ] Edge ✅ / ❌

---

## 🐛 Bugs Découverts

### Bugs Critiques
1. **Bug #1** : ________________
   - **Description** : ________________
   - **Reproduction** : ________________
   - **Impact** : ________________

2. **Bug #2** : ________________
   - **Description** : ________________
   - **Reproduction** : ________________
   - **Impact** : ________________

### Bugs Mineurs
1. **Bug #1** : ________________
   - **Description** : ________________
   - **Reproduction** : ________________

2. **Bug #2** : ________________
   - **Description** : ________________
   - **Reproduction** : ________________

### Améliorations Suggérées
1. **Amélioration #1** : ________________
2. **Amélioration #2** : ________________
3. **Amélioration #3** : ________________

---

## 📝 Notes Finales

### Points Positifs
- ________________
- ________________
- ________________

### Points d'Amélioration
- ________________
- ________________
- ________________

### Recommandations
- ________________
- ________________
- ________________

---

**Date du test** : ________________  
**Testeur** : ________________  
**Version testée** : ________________  
**Environnement** : ________________
