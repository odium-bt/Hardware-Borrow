<?php

namespace HardwareBorrow\Model;

use PDO;
use PDOException;
/*
 *  Classe principale de l'objet de connexion PDO
 */

class Database
{
    private static $instance = null;
    private $connexion;

    private function __construct()
    {
        try {
            $this->connexion = new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . "; charset=utf8mb4", $_ENV['DB_LOGIN'], $_ENV['DB_PASSWORD']);
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Connexion DB échouée : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fonction getInstance
     * résultat : Créée une instance si première utilisation
     *            Retourne l'instance de la connection PDO
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connexion;
    }
}
