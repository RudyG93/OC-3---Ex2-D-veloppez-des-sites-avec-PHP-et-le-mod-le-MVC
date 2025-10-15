<?php 
/**
 * Contrôleur du monitoring et des statistiques.
 * Gère l'affichage des tableaux de bord et des statistiques du blog.
 */
 
class MonitoringController {

    /**
     * Affiche la page de monitoring/statistiques.
     * @return void
     */
    public function showMonitoring() : void
    {
        // On vérifie que l'utilisateur est connecté.
        SessionHelper::checkIfUserIsConnected();

        // Récupération des paramètres de tri
        $sortBy = HttpHelper::get('sort', null); // Pas de tri par défaut
        $sortOrder = HttpHelper::get('order', 'desc'); // Ordre par défaut : décroissant

        // Validation des paramètres
        $allowedSortColumns = ['title', 'date_creation', 'views', 'comment_count'];
        $allowedSortOrders = ['asc', 'desc'];
        
        if ($sortBy !== null && !in_array($sortBy, $allowedSortColumns)) {
            $sortBy = null; // Réinitialise à "aucun tri" si paramètre invalide
        }
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'desc';
        }

        // Récupération des statistiques
        $articleManager = new ArticleManager();
        $commentManager = new CommentManager();

        // Statistiques générales
        $totalArticles = $articleManager->getTotalArticlesCount();
        $totalComments = $commentManager->getTotalCommentsCount();
        $totalViews = $articleManager->getTotalViewsCount();

        // Articles - récupération de tous les articles avec le nombre de commentaires
        $allArticlesWithComments = $articleManager->getAllArticlesWithCommentCount();
        
        // Tri des articles selon les paramètres (seulement si un tri est demandé)
        if ($sortBy !== null) {
            $popularArticles = SortHelper::sortArticlesWithComments($allArticlesWithComments, $sortBy, $sortOrder);
        } else {
            // Aucun tri - ordre naturel de la base de données (par ID croissant généralement)
            $popularArticles = $allArticlesWithComments;
        }
        
        // Limitation à 20 articles pour l'affichage
        $popularArticles = array_slice($popularArticles, 0, 20);

        // Paramètres pour la gestion des commentaires
        $commentSort = HttpHelper::get('comment_sort', null); // null = pas de tri par défaut
        $commentOrder = HttpHelper::get('comment_order', 'desc');
        $commentPage = max(1, (int)HttpHelper::get('comment_page', 1));
        $commentsPerPage = 5;
        $commentOffset = ($commentPage - 1) * $commentsPerPage;

        // Validation des paramètres de tri des commentaires
        $allowedCommentSortColumns = ['date_creation', 'pseudo', 'article_title'];
        if ($commentSort !== null && !in_array($commentSort, $allowedCommentSortColumns)) {
            $commentSort = null; // Réinitialise si invalide
        }
        if (!in_array($commentOrder, $allowedSortOrders)) {
            $commentOrder = 'desc';
        }

        // Commentaires récents (pour la section statistiques)
        $recentComments = $commentManager->getRecentCommentsWithArticleTitle(5);
        
        // Tous les commentaires (pour la modération)
        // Si pas de tri spécifié, utilise 'date_creation' par défaut pour la requête SQL
        $commentSortColumn = $commentSort ?? 'date_creation';
        $allComments = $commentManager->getAllCommentsWithArticleTitle($commentsPerPage, $commentOffset, $commentSortColumn, $commentOrder);

        // Message de succès si suppression
        $successMessage = '';
        if (HttpHelper::get('msg') === 'comment_deleted') {
            $successMessage = 'Commentaire supprimé avec succès !';
        }

        // Statistiques par mois
        $articleStatsByMonth = $articleManager->getArticleStatsByMonth();
        $commentStatsByMonth = $commentManager->getCommentStatsByMonth();

        // Calcul des moyennes
        $averageViewsPerArticle = $totalArticles > 0 ? round($totalViews / $totalArticles, 2) : 0;
        $averageCommentsPerArticle = $totalArticles > 0 ? round($totalComments / $totalArticles, 2) : 0;

        $view = new View("Monitoring - Statistiques");
        $view->render("monitoring", [
            'totalArticles' => $totalArticles,
            'totalComments' => $totalComments,
            'totalViews' => $totalViews,
            'averageViewsPerArticle' => $averageViewsPerArticle,
            'averageCommentsPerArticle' => $averageCommentsPerArticle,
            'popularArticles' => $popularArticles,
            'recentComments' => $recentComments,
            'allComments' => $allComments,
            'articleStatsByMonth' => $articleStatsByMonth,
            'commentStatsByMonth' => $commentStatsByMonth,
            'currentSort' => $sortBy,
            'currentOrder' => $sortOrder,
            'commentSort' => $commentSort,
            'commentOrder' => $commentOrder,
            'commentPage' => $commentPage,
            'commentsPerPage' => $commentsPerPage,
            'successMessage' => $successMessage
        ]);
    }
}
