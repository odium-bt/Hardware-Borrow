<?php

namespace HardwareBorrow\Controller;

/*
 * Classe Route
 * Gère le routage par la méthode $_GET["action"]
*/

class Route
{
    public string $action;

    public function __construct()
    {
        // Récupère $_GET, sinon "accueil" quand vide
        if (!isset($_GET['action'])) {
            $_GET['action'] = "home";
        }
        $this->action = $_GET["action"];
        $this->redirigeVers();
    }

    public function redirigeVers()
    {
        // Ferme la session de l'utilisateur si trop de temps s'est écoulé depuis la dernière activité
        if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
            session_unset();
            session_destroy();
        }
        $_SESSION['last_activity'] = time();


        // Visiteur
        if (!isset($_SESSION['user_id'])) {
            switch ($this->action) {
                case "login":
                    new Login;
                    break;
                case "register":
                    new Register;
                    break;
                default:
                    // Si $_GET["action"] = action non reconnue
                    new Login;
                    break;
            }
        }
        // Utilisateur connecté
        else {
            switch ($this->action) {
                case "home":
                    new Home;
                    break;
                case "hardware":
                    new Hardware;
                    break;
                case "borrow":
                    new Borrow;
                    break;
                case "login" || "register":
                    new AlreadyLoggedIn;
                    break;
                case "logout":
                    session_unset();
                    session_destroy();
                    $_GET['action'] = "login";
                    new Login;
                    break;
                default:
                    // Si $_GET["action"] = action non reconnue
                    new PageNotFound;
                    break;
            }
        }
    }
}
