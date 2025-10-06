# Correction - Erreur SQL LIMIT avec paramètres bindés

## Problème rencontré

**Erreur** : `SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near ''10'' at line 1`

**Page affectée** : `index.php?action=monitoring`

## Cause

L'erreur provenait des requêtes SQL utilisant `LIMIT` avec des paramètres bindés PDO :

```php
// PROBLÉMATIQUE (causait l'erreur)
$sql = "SELECT * FROM article ORDER BY views DESC LIMIT :limit";
$result = $this->db->query($sql, ['limit' => $limit]);
```

**Pourquoi ça ne fonctionne pas** :
- MySQL/MariaDB ne permettent pas d'utiliser des paramètres bindés avec `LIMIT`
- Les paramètres bindés sont échappés avec des guillemets, devenant `LIMIT '10'` au lieu de `LIMIT 10`
- La syntaxe correcte pour `LIMIT` nécessite un nombre entier non quoté

## Solution appliquée

**Méthodes corrigées** :
1. `ArticleManager::getMostPopularArticles()`
2. `CommentManager::getRecentCommentsWithArticleTitle()`

**Changement effectué** :
```php
// AVANT (erreur)
$sql = "SELECT * FROM article ORDER BY views DESC LIMIT :limit";
$result = $this->db->query($sql, ['limit' => $limit]);

// APRÈS (corrigé)
$limit = max(1, (int)$limit); // Sécurisation
$sql = "SELECT * FROM article ORDER BY views DESC LIMIT " . $limit;
$result = $this->db->query($sql); // Pas de paramètres bindés
```

## Sécurisation appliquée

Pour éviter les injections SQL avec la concaténation directe :

1. **Validation stricte** : `$limit = max(1, (int)$limit);`
   - Conversion forcée en entier avec `(int)`
   - Valeur minimum de 1 avec `max(1, ...)`
   - Impossible d'injecter du SQL malveillant

2. **Contrôle des valeurs** : Les `$limit` viennent du code PHP contrôlé, pas de l'utilisateur

## Méthodes non affectées

Ces méthodes utilisent `LIMIT` en dur et fonctionnent correctement :
- `ArticleManager::getArticleStatsByMonth()` → `LIMIT 12`
- `CommentManager::getCommentStatsByMonth()` → `LIMIT 12`

## Alternatives pour l'avenir

Pour les cas où `LIMIT` doit être dynamique, trois approches sécurisées :

### 1. Concaténation sécurisée (utilisée ici)
```php
$limit = max(1, min(100, (int)$limit)); // Validation + plafond
$sql = "SELECT * FROM table LIMIT " . $limit;
```

### 2. Paramètres positionnels (alternative)
```php
$stmt = $pdo->prepare("SELECT * FROM table LIMIT ?");
$stmt->execute([$limit]);
```

### 3. Requête préparée manuelle
```php
$stmt = $pdo->prepare("SELECT * FROM table ORDER BY col DESC");
$stmt->execute();
$results = array_slice($stmt->fetchAll(), 0, $limit);
```

## Test de validation

Pour vérifier que la correction fonctionne :
1. ✅ Accéder à `index.php?action=monitoring`
2. ✅ Vérifier l'affichage des "Articles les Plus Populaires"
3. ✅ Vérifier l'affichage des "Commentaires Récents"
4. ✅ Pas d'erreur SQL dans les logs

## Leçon retenue

- ❌ **Ne jamais** utiliser `LIMIT :param` avec des paramètres bindés
- ✅ **Toujours** valider et convertir en entier avant concaténation
- ✅ **Préférer** les LIMIT fixes quand c'est possible
- ✅ **Documenter** les contraintes SQL spécifiques dans les commentaires