<?php

/**
 * Classe helper pour la gestion des sessions utilisateur.
 * Cette classe contient des méthodes statiques pour vérifier la connexion et déconnecter l'utilisateur.
 */
class SessionHelper
{
    /**
     * Vérifie si l'utilisateur est connecté.
     * Redirige vers le formulaire de connexion si ce n'est pas le cas.
     * @return void
     */
    public static function checkIfUserIsConnected() : void
    {
        if (!isset($_SESSION['user'])) {
            HttpHelper::redirect("connectionForm");
        }
    }

    /**
     * Déconnecte l'utilisateur en supprimant les données de session.
     * @return void
     */
    public static function disconnectUser() : void
    {
        unset($_SESSION['user']);
    }
}
