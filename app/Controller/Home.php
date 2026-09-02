<?php

namespace HardwareBorrow\Controller;
/*
 * Classe Home
 * Gère la page d'accueil
 */

class Home
{
    public function __construct()
    {
        require ROOT . "/app/View/accueil_view.php";
    }
}
