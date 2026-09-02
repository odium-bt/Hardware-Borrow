<?php

namespace HardwareBorrow\Controller;
/*
 * Classe AlreadyLoggedIn
 * Affiche la page déjà connecté
 */

class AlreadyLoggedIn
{
    public function __construct()
    {
        require ROOT . "/app/View/already-logged-in.php";
    }
}
