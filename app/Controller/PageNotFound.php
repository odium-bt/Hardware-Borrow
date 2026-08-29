<?php

namespace CoteInfo\Controller;
/*
 * Classe PageNotFound
 * Gère la page 404
 */

class PageNotFound
{
    public function __construct()
    {
        require ROOT . "/app/View/404_view.php";
    }
}
