# Correction - Gestion des valeurs NULL dans les entités

## Problème rencontré

**Erreur** : `TypeError: Article::setDateUpdate(): Argument #1 ($dateUpdate) must be of type DateTime|string, null given`

## Cause

La méthode `setDateUpdate()` dans la classe `Article` n'acceptait que les types `string|DateTime` mais pas `null`. Cependant :

1. Le champ `date_update` dans la base de données est défini comme `datetime DEFAULT NULL`
2. Les articles qui n'ont jamais été modifiés ont `date_update = NULL` dans la base
3. Lors de l'hydratation automatique via `AbstractEntity::hydrate()`, la valeur `null` était passée au setter
4. Le setter rejetait cette valeur avec une TypeError

## Solution appliquée

**Modification du setter `setDateUpdate()`** dans `models/Article.php` :

```php
// AVANT (causait l'erreur)
public function setDateUpdate(string|DateTime $dateUpdate, string $format = 'Y-m-d H:i:s') : void

// APRÈS (corrigé)
public function setDateUpdate(string|DateTime|null $dateUpdate, string $format = 'Y-m-d H:i:s') : void
{
    if ($dateUpdate === null) {
        $this->dateUpdate = null;
    } elseif (is_string($dateUpdate)) {
        $dateUpdate = DateTime::createFromFormat($format, $dateUpdate);
        $this->dateUpdate = $dateUpdate;
    } else {
        $this->dateUpdate = $dateUpdate;
    }
}
```

## Changements effectués

1. **Type hint étendu** : `string|DateTime|null` au lieu de `string|DateTime`
2. **Gestion explicite du null** : Vérification `if ($dateUpdate === null)`
3. **Documentation mise à jour** : Docblock avec `@param string|DateTime|null`

## Bonnes pratiques pour l'avenir

### Pour les setters d'entités :
- ✅ Toujours vérifier si le champ DB peut être NULL
- ✅ Adapter le type hint en conséquence : `type|null`
- ✅ Gérer explicitement le cas null dans la méthode
- ✅ Tester avec des données réelles de la base

### Pour les champs de date optionnels :
- `date_creation` → `datetime NOT NULL` → setter accepte `string|DateTime`
- `date_update` → `datetime DEFAULT NULL` → setter accepte `string|DateTime|null`

## Impact

Cette correction permet :
- ✅ L'hydratation correcte des articles depuis la base de données
- ✅ Le fonctionnement normal de toutes les pages (admin, monitoring, etc.)
- ✅ La gestion propre des articles non modifiés (date_update = null)

## Test de validation

Pour vérifier que la correction fonctionne :
1. Accéder à `index.php?action=admin` (devrait maintenant fonctionner)
2. Accéder à `index.php?action=monitoring` (devrait afficher les statistiques)
3. Consulter des articles (devrait incrémenter les vues sans erreur)

Cette erreur était un problème classique de discordance entre le schéma de base de données (nullable) et les types PHP (non-nullable).