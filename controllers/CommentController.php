<?php
/**
 * Contrôleur de gestion des commentaires.
 * Gère l'ajout de commentaires (partie publique) et la modération (partie admin).
 */

class CommentController 
{
    /**
     * Ajoute un commentaire (partie publique).
     * @return void
     */
    public function addComment() : void
    {
        // Récupération des données du formulaire.
        $pseudo = HttpHelper::get("pseudo");
        $content = HttpHelper::get("content");
        $idArticle = HttpHelper::get("idArticle");

        // On vérifie que les données sont valides.
        if (empty($pseudo) || empty($content) || empty($idArticle)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        // On vérifie que l'article existe.
        $articleManager = new ArticleManager();
        $article = $articleManager->getArticleById($idArticle);
        if (!$article) {
            throw new Exception("L'article demandé n'existe pas.");
        }

        // On crée l'objet Comment.
        $comment = new Comment([
            'pseudo' => $pseudo,
            'content' => $content,
            'idArticle' => $idArticle
        ]);

        // On ajoute le commentaire.
        $commentManager = new CommentManager();
        $result = $commentManager->addComment($comment);

        // On vérifie que l'ajout a bien fonctionné.
        if (!$result) {
            throw new Exception("Une erreur est survenue lors de l'ajout du commentaire.");
        }

        // On redirige vers la page de l'article.
        HttpHelper::redirect("showArticle", ['id' => $idArticle]);
    }

    /**
     * Supprime un commentaire (partie admin).
     * @return void
     */
    public function deleteComment() : void
    {
        // On vérifie que l'utilisateur est connecté.
        SessionHelper::checkIfUserIsConnected();

        $id = HttpHelper::get("id", -1);
        $returnUrl = HttpHelper::get("return", "monitoring");

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
            HttpHelper::redirect("monitoring", ['msg' => 'comment_deleted']);
        } else {
            HttpHelper::redirect("admin");
        }
    }
}