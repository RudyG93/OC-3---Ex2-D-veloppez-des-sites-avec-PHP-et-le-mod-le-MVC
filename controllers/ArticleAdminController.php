<?php 
/**
 * Contrôleur de gestion des articles (partie admin).
 * Gère les opérations CRUD sur les articles pour l'administration.
 */
 
class ArticleAdminController {

    /**
     * Affiche la page d'administration des articles.
     * @return void
     */
    public function showAdmin() : void
    {
        // On vérifie que l'utilisateur est connecté.
        SessionHelper::checkIfUserIsConnected();

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
     * Affiche le formulaire d'ajout d'un nouvel article.
     * @return void
     */
    public function showAddArticleForm() : void
    {
        // On vérifie que l'utilisateur est connecté.
        SessionHelper::checkIfUserIsConnected();

        // On crée un article vide pour le formulaire.
        $article = new Article();

        // On affiche la page d'ajout de l'article.
        $view = new View("Ajouter un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Affiche le formulaire de modification d'un article existant.
     * @return void
     */
    public function showUpdateArticleForm() : void 
    {
        // On vérifie que l'utilisateur est connecté.
        SessionHelper::checkIfUserIsConnected();

        // On récupère l'id de l'article.
        $id = HttpHelper::get("id", -1);

        // On récupère l'article associé.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($id);

        // Si l'article n'existe pas, on génère une erreur.
        if (!$article) {
            throw new Exception("L'article demandé n'existe pas.");
        }

        // On affiche la page de modification de l'article.
        $view = new View("Modifier un article");
        $view->render("updateArticleForm", [
            'article' => $article
        ]);
    }

    /**
     * Ajoute un nouvel article.
     * @return void
     */
    public function addArticle() : void 
    {
        // On vérifie que l'utilisateur est connecté.
        SessionHelper::checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $title = HttpHelper::get("title");
        $content = HttpHelper::get("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => -1, // -1 pour indiquer un nouvel article
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On ajoute l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        HttpHelper::redirect("admin");
    }

    /**
     * Modifie un article existant.
     * @return void
     */
    public function updateArticle() : void 
    {
        // On vérifie que l'utilisateur est connecté.
        SessionHelper::checkIfUserIsConnected();

        // On récupère les données du formulaire.
        $id = HttpHelper::get("id", -1);
        $title = HttpHelper::get("title");
        $content = HttpHelper::get("content");

        // On vérifie que les données sont valides.
        if (empty($title) || empty($content)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        // On vérifie que l'article existe.
        if ($id == -1) {
            throw new Exception("L'article à modifier n'existe pas.");
        }

        // On crée l'objet Article.
        $article = new Article([
            'id' => $id,
            'title' => $title,
            'content' => $content,
            'id_user' => $_SESSION['idUser']
        ]);

        // On modifie l'article.
        $articleManager = new ArticleManager();
        $articleManager->addOrUpdateArticle($article);

        // On redirige vers la page d'administration.
        HttpHelper::redirect("admin");
    }

    /**
     * Suppression d'un article.
     * @return void
     */
    public function deleteArticle() : void
    {
        // On vérifie que l'utilisateur est connecté.
        SessionHelper::checkIfUserIsConnected();

        $id = HttpHelper::get("id", -1);

        // On supprime l'article.
        $articleManager = new ArticleManager();
        $articleManager->deleteArticle($id);
       
        // On redirige vers la page d'administration.
        HttpHelper::redirect("admin");
    }
}
