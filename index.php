<?php

require_once 'config/config.php';
require_once 'config/autoload.php';

// On récupère l'action demandée par l'utilisateur.
// Si aucune action n'est demandée, on affiche la page d'accueil.
$action = HttpHelper::get('action', 'home');

// Try catch global pour gérer les erreurs
try {
    // Pour chaque action, on appelle le bon contrôleur et la bonne méthode.
    switch ($action) {
        // Pages accessibles à tous.
        case 'home':
            $articleController = new ArticleController();
            $articleController->showHome();
            break;

        case 'apropos':
            $articleController = new ArticleController();
            $articleController->showApropos();
            break;
        
        case 'showArticle': 
            $articleController = new ArticleController();
            $articleController->showArticle();
            break;

        case 'addComment':
            $commentController = new CommentController();
            $commentController->addComment();
            break;

        // Section admin & connexion. 
        case 'admin': 
            $articleAdminController = new ArticleAdminController();
            $articleAdminController->showAdmin();
            break;

        case 'connectionForm':
            $authController = new AuthController();
            $authController->displayConnectionForm();
            break;

        case 'connectUser': 
            $authController = new AuthController();
            $authController->connectUser();
            break;

        case 'disconnectUser':
            $authController = new AuthController();
            $authController->disconnectUser();
            break;

        case 'showAddArticleForm':
            $articleAdminController = new ArticleAdminController();
            $articleAdminController->showAddArticleForm();
            break;

        case 'addArticle':
            $articleAdminController = new ArticleAdminController();
            $articleAdminController->addArticle();
            break;

        case 'showUpdateArticleForm':
            $articleAdminController = new ArticleAdminController();
            $articleAdminController->showUpdateArticleForm();
            break;

        case 'updateArticle': 
            $articleAdminController = new ArticleAdminController();
            $articleAdminController->updateArticle();
            break;

        case 'deleteArticle':
            $articleAdminController = new ArticleAdminController();
            $articleAdminController->deleteArticle();
            break;

        case 'monitoring':
            $monitoringController = new MonitoringController();
            $monitoringController->showMonitoring();
            break;

        case 'deleteComment':
            $commentController = new CommentController();
            $commentController->deleteComment();
            break;

        default:
            throw new Exception("La page demandée n'existe pas.");
    }
} catch (Exception $e) {
    // En cas d'erreur, on affiche la page d'erreur.
    $errorView = new View('Erreur');
    $errorView->render('errorPage', ['errorMessage' => $e->getMessage()]);
}
