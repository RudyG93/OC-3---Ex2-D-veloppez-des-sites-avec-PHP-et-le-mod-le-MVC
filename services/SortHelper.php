<?php

/**
 * Classe helper pour la gestion du tri des données.
 * Cette classe contient des méthodes statiques pour générer des liens de tri et des indicateurs visuels.
 */
class SortHelper
{
    /**
     * Génère un lien pour trier une colonne.
     * @param string $column : La colonne à trier.
     * @param string|null $currentSort : La colonne actuellement triée.
     * @param string $currentOrder : L'ordre actuel (asc/desc).
     * @param string $paramPrefix : Préfixe des paramètres ('sort' ou 'comment_sort').
     * @return string : Le lien généré.
     */
    public static function generateSortLink(string $column, ?string $currentSort, string $currentOrder, string $paramPrefix = 'sort') : string
    {
        $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
        $orderParam = $paramPrefix === 'sort' ? 'order' : 'comment_order';

        return "index.php?action=monitoring&{$paramPrefix}={$column}&{$orderParam}={$newOrder}";
    }

    /**
     * Génère un indicateur de tri pour une colonne.
     * @param string $column : La colonne à vérifier.
     * @param string|null $currentSort : La colonne actuellement triée.
     * @param string $currentOrder : L'ordre actuel (asc/desc).
     * @return string : L'indicateur HTML.
     */
    public static function getSortIndicator(string $column, ?string $currentSort, string $currentOrder) : string
    {
        if ($currentSort === $column) {
            return '<span class="sort-indicator ' . $currentOrder . '"></span>';
        }
        
        return '<span class="sort-indicator neutral"></span>';
    }

    /**
     * Retourne une valeur de tri pour un article selon un critère.
     * @param Article $article : L'article à trier.
     * @param string $sortBy : Le critère de tri.
     * @return mixed : La valeur utilisée pour le tri.
     */
    public static function getArticleSortValue(Article $article, string $sortBy)
    {
        switch ($sortBy) {
            case 'title':
                return $article->getTitle();
            case 'date_creation':
                return $article->getDateCreation();
            case 'views':
                return $article->getViews();
            default:
                return $article->getViews();
        }
    }

    /**
     * Trie un tableau d'articles avec commentaires selon le critère et l'ordre spécifiés.
     * @param array $articlesWithComments : tableau d'éléments ['article' => Article, 'comment_count' => int]
     * @param string $sortBy : critère de tri ('title', 'date_creation', 'views', 'comment_count')
     * @param string $sortOrder : ordre de tri ('asc', 'desc')
     * @return array : tableau trié
     */
    public static function sortArticlesWithComments(array $articlesWithComments, string $sortBy, string $sortOrder) : array
    {
        usort($articlesWithComments, function($a, $b) use ($sortBy, $sortOrder) {
            if ($sortBy === 'comment_count') {
                $valueA = $a['comment_count'];
                $valueB = $b['comment_count'];
            } else {
                $valueA = self::getArticleSortValue($a['article'], $sortBy);
                $valueB = self::getArticleSortValue($b['article'], $sortBy);
            }

            // Comparaison selon le type de valeur
            if (is_string($valueA) && is_string($valueB)) {
                $comparison = strcasecmp($valueA, $valueB);
            } elseif ($valueA instanceof DateTime && $valueB instanceof DateTime) {
                $comparison = $valueA <=> $valueB;
            } else {
                $comparison = $valueA <=> $valueB;
            }

            // Inversion si ordre décroissant
            return $sortOrder === 'desc' ? -$comparison : $comparison;
        });

        return $articlesWithComments;
    }
}
