<?php

namespace HardwareBorrow\Model;

use PDO;

/**
 * Classe gérant la table Hardware
 */

class HardwareModel extends Model
{
    public function __construct()
    {
        $this->tableName = "hardware";
        $this->idName = "id_hardware";
        parent::__construct();
    }

}
