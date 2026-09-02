<?php

namespace HardwareBorrow\Controller;
/*
 * Classe Borrow
 * Gère la page des emprunts
 */

class Borrow
{
    public function __construct()
    {
        require ROOT . "/app/View/borrow_view.php";
    }
}
