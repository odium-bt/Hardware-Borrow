<?php

namespace CoteInfo\Controller;

use CoteInfo\Model\UserModel;
/*
 * Contrôle de qualité du formulaire de connexion
 * Filtre les champs avant de refuser ou valider une connexion utilisateur
 */

class Login extends LoginForm
{
    public function __construct()
    {
        parent::__construct();

        // Si $_POST est vide (début) ou qu'il y a des erreurs, affiche le formulaire
        if (empty($_POST) || !empty($this->errors)) {
            require ROOT . '/app/View/connexion_view.php'; // Retour au formulaire
        }
        // Sinon $_POST est rempli sans erreurs, affiche la page succès et connecte l'utilisateur
        else {
            if (!isset($user)) {
                $user = new UserModel;
            }
            $_SESSION['user_id'] = $user->getIdByEmail($this->email);
            $_SESSION['is_admin'] = $user->isAdmin($_SESSION['user_id']);
            session_regenerate_id(true);
            require ROOT . '/app/View/connexion-success_view.php'; // Affiche page succès
        }
    }
}
