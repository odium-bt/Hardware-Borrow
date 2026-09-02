<?php

namespace HardwareBorrow\Controller;

use HardwareBorrow\Model\HardwareModel;
/*
 * Classe Hardware
 * Gère la page de la liste du matériel
 */

class Hardware
{
    protected HardwareModel $HardwareModel;

    public function __construct()
    {
        $this->HardwareModel = new HardwareModel;

        $hardware = $this->HardwareModel->getAll();
        
        require ROOT . "/app/View/hardware_view.php";
    }

}
