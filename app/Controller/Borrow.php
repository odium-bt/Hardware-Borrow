<?php

namespace HardwareBorrow\Controller;

use HardwareBorrow\Model\HardwareModel;
use HardwareBorrow\Model\ReservationModel;

/*
 * Classe Borrow
 * Gère la page des emprunts
 */

class Borrow
{
    public $errors = [];
    protected string $hardware = "";
    protected string $date_start = "";
    protected string $date_end = "";

    public function __construct()
    {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $post_element) {
                switch ($key) {
                    case 'hardware':
                        $this->hardware = trim($_POST['hardware']);
                        break;
                    case 'date_start':
                        $this->date_start = trim($_POST['date_start']);
                        break;
                    case 'date_end':
                        $this->date_end = strtolower(trim($_POST['date_end']));
                        break;
                }
            }

            // Contrôle de qualité (trouve les erreurs et ajoute les messages dans le tableau $errors)
            // Hardware
            if ($this->hardware === "") {
                $this->errors['hardware'] = "Requis";
            }
            // Date de début
            if ($this->date_start === "") {
                $this->errors['date_start'] = "Requis";
            }
            // Date de fin
            if ($this->date_end === "") {
                $this->errors['date_end'] = "Requis";
            }
        }

        // ============ Sortie
        // Si $_POST est vide ou qu'il y a des erreurs, affiche le formulaire
        if (empty($_POST) || !empty($this->errors)) {
            $hardwareModel = new HardwareModel;
            $hardwareList = $hardwareModel->getAll();
            require ROOT . "/app/View/borrow_view.php";
        }
        // Si $_POST est rempli sans erreurs
        else {
            $idUser = $_SESSION['user_id'];
            $ReservationModel = new ReservationModel;
                $idReservation = $ReservationModel->createReservation($this->date_start, $this->date_end, $idUser);
                $ReservationModel->addItem($idReservation, $this->hardware);
                require ROOT . '/app/View/borrow-success_view.php'; // affiche page succès
        }
        // ============
    }
}
