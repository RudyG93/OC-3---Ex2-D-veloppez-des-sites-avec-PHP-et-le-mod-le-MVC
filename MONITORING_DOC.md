# Page de Monitoring - Documentation

## Vue d'ensemble

La page de monitoring a été créée pour fournir aux administrateurs une vue d'ensemble complète des statistiques du blog d'Emilie Forteroche.

## Accès à la page

**URL** : `index.php?action=monitoring`

**Prérequis** : L'utilisateur doit être connecté en tant qu'administrateur (session 'user' active)

**Navigation** :
- Lien "Statistiques" dans la barre de navigation (visible uniquement si connecté)
- Bouton "📊 Voir les Statistiques" dans la page d'administration
- Accès direct via l'URL

## Fonctionnalités affichées

### 1. Statistiques générales (cartes en haut)
- **Articles Publiés** : Nombre total d'articles
- **Commentaires** : Nombre total de commentaires
- **Vues Totales** : Somme de toutes les vues de tous les articles
- **Vues/Article** : Moyenne des vues par article
- **Commentaires/Article** : Moyenne des commentaires par article

### 2. Articles les plus populaires
- Tableau classé par nombre de vues décroissant
- Affiche : rang, titre (avec lien), date de création, nombre de vues, lien de modification
- Limité aux 10 articles les plus vus

### 3. Commentaires récents
- Liste des 10 derniers commentaires
- Affiche : auteur, date, article concerné, extrait du commentaire (200 caractères)
- Zone avec défilement si nécessaire

### 4. Évolution temporelle
- **Graphiques par mois** : Nombre d'articles et commentaires créés par mois
- Affichage sur les 12 derniers mois
- Présentation en blocs avec mois/année et compteurs

### 5. Informations système
- Date/heure de dernière consultation
- Version PHP utilisée
- Limite mémoire configurée

## Design et responsivité

- **Optimisé pour écrans ≥ 1366px** (comme demandé)
- Design moderne avec dégradés et ombres
- Grille responsive pour les statistiques
- Couleurs cohérentes avec le thème (#667eea)
- Effets hover sur les cartes
- Animations CSS subtiles

## Fichiers créés/modifiés

### Nouveaux fichiers
- `views/templates/monitoring.php` : Template principal de la page

### Fichiers modifiés
- `controllers/AdminController.php` : Ajout de `showMonitoring()`
- `models/ArticleManager.php` : Méthodes de statistiques
- `models/CommentManager.php` : Méthodes de statistiques
- `views/templates/admin.php` : Lien vers monitoring
- `views/templates/main.php` : Navigation mise à jour
- `index.php` : Route 'monitoring' ajoutée

## Méthodes ajoutées

### ArticleManager
- `getTotalArticlesCount()` : Compte total des articles
- `getTotalViewsCount()` : Somme de toutes les vues
- `getMostPopularArticles($limit)` : Articles triés par vues
- `getArticleStatsByMonth()` : Statistiques mensuelles d'articles

### CommentManager
- `getTotalCommentsCount()` : Compte total des commentaires
- `getRecentCommentsWithArticleTitle($limit)` : Commentaires récents avec titre d'article
- `getCommentStatsByMonth()` : Statistiques mensuelles de commentaires

## Sécurité

- ✅ Vérification de l'authentification admin via `checkIfUserIsConnected()`
- ✅ Utilisation de `Utils::format()` pour échapper les données affichées
- ✅ Requêtes préparées dans tous les managers

## Performance

- Requêtes optimisées avec LIMIT appropriés
- Utilisation d'index sur les dates pour les statistiques mensuelles
- Jointures efficaces pour les commentaires avec titres d'articles

## Tests recommandés

1. **Accès sécurisé** : Vérifier que la page nécessite une authentification
2. **Données correctes** : Comparer les statistiques avec les données réelles
3. **Navigation** : Tester tous les liens de navigation
4. **Affichage** : Vérifier sur différentes résolutions ≥ 1366px
5. **Performance** : Mesurer le temps de chargement avec beaucoup de données