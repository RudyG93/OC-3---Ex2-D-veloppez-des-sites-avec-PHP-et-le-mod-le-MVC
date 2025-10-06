<?php 
/**
 * Contrôleur de la partie admin.
 */
 
class AdminController {

    /**
     * Affiche la page d'administration.
     * @return void
     */
    public function showAdmin() : void
    {
        // On vérifie que l'utilisateur est connecté.
        $this->checkIfUserIsConnected();

        // On récupère les articles.
        $articleManager = new ArticleManager();
        $articles = $articleManager->getAllArticles();

        // On affiche la page d'administration.
        $view = new View("Administration");
        $view->render("admin", [
            'articles' => $articles
        ]);
    }

    /**
     * Vérifie que l'utilisateur est connecté.
     * @return void
     */
    private function checkIfUserIsConnected() : void
    {
        // On vérifie que l'utilisateur est connecté.
        if (!isset($_SESSION['user'])) {
            Utils::redirect("connectionForm");
        }
    }

    /**
     * Affichage du formulaire de connexion.
     * @return void
     */
    public function displayConnectionForm() : void 
    {
        $view = new View("Connexion");
        $view->render("connectionForm");
    }

    /**
     * Connexion de l'utilisateur.
     * @return void
     */
    public function connectUser() : void 
    {
        // On récupère les données du formulaire.
        $login = Utils::request("login");
        $password = Utils::request("password");

        // On vérifie que les données sont valides.
        if (empty($login) || empty($password)) {
            throw new Exception("Tous les champs sont obligatoires. 1");
        }

        // On vérifie que l'utilisateur existe.
        $userManager = new UserManager();
        $user = $userManager->getUserByLogin($login);
        if (!$user) {
            throw new Exception("L'utilisateur demandé n'existe pas.");
        }

        // On vérifie que le mot de passe est correct.
        if (!password_verify($password, $user->getPassword())) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            throw new Exception("Le mot de passe est incorrect : $hash");
        }

        // On connecte l'utilisateur.
        $_SESSION['user'] = $user;
        $_SESSION['idUser'] = $user->getId();

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Déconnexion de l'utilisateur.
     * @return void
     */
    public function disconnectUser() : void 
    {
        // On déconnecte l'utilisateur.
        unset($_SESSION['user']);

        // On redirige vers la page d'accueil.
        Utils::redirect("home");
    }

    /**
     * Affichage du formulaire d'ajout d'un article.
     * @return void
     */
    public function showUpdateArticleForm() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère l'id de l'article s'il existe.
        $id = Utils::request("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on en crée un vide. 
        if (!$article) {
            $article = new Article();
        }

        // On affiche la page de modification de l'article.
        $view = new View("Edition d'un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajout et modification d'un article. 
     * On sait si un article est ajouté car l'id vaut -1.
     * @return void
     */
    public function updateArticle() : void 
    {
        $this->checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = Utils::request("id", -1);
        $title = Utils::request("title");
        $content = Utils::request("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires. 2");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id, // Si l'id vaut -1, l'article sera ajouté. Sinon, il sera modifié.
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }


    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle() : void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);
       
        // On redirige vers la page d'administration.
        Utils::redirect("admin");
    }

    /**
     * Affiche la page de monitoring/statistiques.
     * @return void
     */
    public function showMonitoring() : void
    {
        $this->checkIfUserIsConnected();

        // Récupération des paramètres de tri
        $sortBy = Utils::request('sort', null); // Pas de tri par défaut
        $sortOrder = Utils::request('order', 'desc'); // Ordre par défaut : décroissant

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
            $popularArticles = $this->sortArticlesWithComments($allArticlesWithComments, $sortBy, $sortOrder);
        } else {
            // Aucun tri - ordre naturel de la base de données (par ID croissant généralement)
            $popularArticles = $allArticlesWithComments;
        }
        
        // Limitation à 20 articles pour l'affichage
        $popularArticles = array_slice($popularArticles, 0, 20);

        // Paramètres pour la gestion des commentaires
        $commentSort = Utils::request('comment_sort', 'date_creation');
        $commentOrder = Utils::request('comment_order', 'desc');
        $commentPage = max(1, (int)Utils::request('comment_page', 1));
        $commentsPerPage = 5;
        $commentOffset = ($commentPage - 1) * $commentsPerPage;

        // Validation des paramètres de tri des commentaires
        $allowedCommentSortColumns = ['date_creation', 'pseudo', 'article_title'];
        if (!in_array($commentSort, $allowedCommentSortColumns)) {
            $commentSort = 'date_creation';
        }
        if (!in_array($commentOrder, $allowedSortOrders)) {
            $commentOrder = 'desc';
        }

        // Commentaires récents (pour la section statistiques)
        $recentComments = $commentManager->getRecentCommentsWithArticleTitle(5);
        
        // Tous les commentaires (pour la modération)
        $allComments = $commentManager->getAllCommentsWithArticleTitle($commentsPerPage, $commentOffset, $commentSort, $commentOrder);

        // Message de succès si suppression
        $successMessage = '';
        if (Utils::request('msg') === 'comment_deleted') {
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

    /**
     * Trie un tableau d'articles selon le critère et l'ordre spécifiés.
     * @param array $articles : tableau d'objets Article
     * @param string $sortBy : critère de tri ('title', 'date_creation', 'views')
     * @param string $sortOrder : ordre de tri ('asc', 'desc')
     * @return array : tableau trié d'objets Article
     */
    private function sortArticles(array $articles, string $sortBy, string $sortOrder) : array
    {
        usort($articles, function($a, $b) use ($sortBy, $sortOrder) {
            $valueA = $this->getArticleSortValue($a, $sortBy);
            $valueB = $this->getArticleSortValue($b, $sortBy);
            
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
        
        return $articles;
    }

    /**
     * Récupère la valeur de tri pour un article selon le critère.
     * @param Article $article : l'article
     * @param string $sortBy : le critère de tri
     * @return mixed : la valeur à utiliser pour le tri
     */
    private function getArticleSortValue(Article $article, string $sortBy)
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
    private function sortArticlesWithComments(array $articlesWithComments, string $sortBy, string $sortOrder) : array
    {
        usort($articlesWithComments, function($a, $b) use ($sortBy, $sortOrder) {
            if ($sortBy === 'comment_count') {
                $valueA = $a['comment_count'];
                $valueB = $b['comment_count'];
            } else {
                $valueA = $this->getArticleSortValue($a['article'], $sortBy);
                $valueB = $this->getArticleSortValue($b['article'], $sortBy);
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

    /**
     * Supprime un commentaire.
     * @return void
     */
    public function deleteComment() : void
    {
        $this->checkIfUserIsConnected();

        $id = Utils::request("id", -1);
        $returnUrl = Utils::request("return", "monitoring");

        if ($id <= 0) {
            throw new Exception("ID de commentaire invalide.");
        }

        // Suppression du commentaire
        $commentManager = new CommentManager();
        $success = $commentManager->deleteCommentById($id);

        if (!$success) {
            throw new Exception("Erreur lors de la suppression du commentaire.");
        }

        // Redirection vers la page de retour avec message de succès
        if ($returnUrl === "monitoring") {
            Utils::redirect("monitoring", ['msg' => 'comment_deleted']);
        } else {
            Utils::redirect("admin");
        }
    }
}