# Instructions pour ajouter le comptage des vues

## Mise à jour de la base de données

Pour ajouter le système de comptage des vues, vous devez exécuter le script de migration sur votre base de données.

### Option 1 : Via phpMyAdmin
1. Ouvrez phpMyAdmin
2. Sélectionnez votre base de données `blog_forteroche` (ou `projet3` selon votre configuration)
3. Cliquez sur l'onglet "SQL"
4. Copiez et collez le contenu du fichier `migration_add_views.sql`
5. Cliquez sur "Exécuter"

### Option 2 : Via la ligne de commande
```bash
mysql -u root -p blog_forteroche < migration_add_views.sql
```

### Option 3 : Pour une nouvelle installation
Si vous créez une nouvelle base de données, utilisez simplement le fichier `blog_forteroche.sql` mis à jour qui inclut déjà le champ `views`.

## Fonctionnalités ajoutées

1. **Champ `views` dans la table `article`** : Stocke le nombre de vues de chaque article
2. **Incrémentation automatique** : Chaque fois qu'un article est consulté, le compteur est incrémenté
3. **Affichage des vues** : Le nombre de vues est affiché sur la page d'accueil et sur la page de détail de l'article
4. **Gestion du pluriel** : "1 vue" vs "2 vues"

## Fichiers modifiés

- `blog_forteroche.sql` : Structure de table mise à jour avec le champ `views`
- `models/Article.php` : Ajout de la propriété `views` et de ses getters/setters
- `models/ArticleManager.php` : Ajout de la méthode `incrementViews()`
- `controllers/ArticleController.php` : Incrémentation des vues lors de l'affichage d'un article
- `views/templates/home.php` : Affichage du nombre de vues sur la page d'accueil
- `views/templates/detailArticle.php` : Affichage du nombre de vues sur la page de détail

## Test de la fonctionnalité

1. Appliquez la migration à votre base de données
2. Visitez un article plusieurs fois
3. Vérifiez que le compteur de vues s'incrémente à chaque visite
4. Vérifiez l'affichage correct du pluriel (1 vue / 2 vues)