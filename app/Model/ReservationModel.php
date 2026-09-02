<?php

namespace HardwareBorrow\Model;

/**
 * Classe gérant la table Reservation
 */

class ReservationModel extends Model
{
    public function __construct()
    {
        $this->tableName = "reservation";
        $this->idName = "id_reservation";
        parent::__construct();
    }

    /**
     * Sauvegarde une réservation
     * @param string $dateStart
     * @param string $dateEnd
     * @param int $idUser
     * @return int id de la réservation
     */
    public function createReservation(string $dateStart, string $dateEnd, int $idUser): int
    {
        return $this->dbRequest(
            "INSERT INTO reservation (date_start, date_end, id_user)
         VALUES (:date_start, :date_end, :id_user)",
            [
                'date_start' => $dateStart,
                'date_end' => $dateEnd,
                'id_user' => $idUser
            ],
            true
        );
    }

    /**
     * Ajoute les items à la table associative reservation_items
     * @param int $idReservation
     * @param int $idHardware
     */
    public function addItem(int $idReservation, int $idHardware): void
    {
        $this->dbRequest(
            "INSERT INTO reservation_items (id_reservation, id_hardware)
         VALUES (:id_reservation, :id_hardware)",
            [
                'id_reservation' => $idReservation,
                'id_hardware' => $idHardware
            ]
        );
    }
}
