<?php

namespace HardwareBorrow\Model;

use PDO;
use PDOException;

/*
 * Classe Model
 * Parente les autres classes Model
 */

abstract class Model
{
    protected string $tableName;
    protected string $idName;
    protected PDO $dbConnector;

    protected function __construct()
    {
        // Obtiens la connection PDO
        $this->dbConnector = Database::getInstance()->getConnection();
    }

    /**
     * Fonction dbRequest
     * paramètres : - requête sql
     *              - array de paramètres (optionnel)
     *              - booléen (optionnel)
     * résultat : Exécute le statement PDO et retourne le résultat sous forme de tableau associatif
     *            Si $r = true (méthode save()), retourne l'ID du dernier élément inséré
     */
    protected function dbRequest(string $sql, array $params = [], bool $r = false)
    {
        try {
            $stmt = $this->dbConnector->prepare($sql);
            $stmt->execute($params);

            if ($r === false) { // retourne tableau associatif
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return empty($result) ? null : $result;
            } else {            // retourne ID de la dernière ligne insérée
                return $this->dbConnector->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            error_log($e->getMessage());

            throw $e;
        }
    }

    /*
     * Fonction dbRequestAll
     * paramètres : - requête sql  
     *              - array de paramètres (optionnel)
     * résultat : Exécute le statement PDO et retourne tous les résultats sous forme de tableau associatif
     */
    protected function dbRequestAll(string $sql, array $params = [])
    {
        try {
            $stmt = $this->dbConnector->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return empty($result) ? null : $result;
        } catch (PDOException $e) {
            error_log("SQL: " . $sql);
            error_log("Params: " . json_encode($params));
            error_log($e->getMessage());

            throw $e;
        }
    }

    /*
     * Fonction getAll
     * paramètres : /
     * résultat : Retourne tous les éléments de la table
     */
    public function getAll()
    {
        try {
            return $this->dbRequestAll("SELECT * FROM " . $this->tableName) ?? [];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /*
     * Fonction getById
     * paramètres : id
     * résultat : Retourne l'élément avec l'id donné
     */
    public function getById(int $id)
    {
        try {
            return $this->dbRequest(
                "SELECT * FROM `" . $this->tableName . "` WHERE `$this->idName` = ?",
                [$id]
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /*
     * Fonction getAllById
     * paramètres : id
     * résultat : Retourne l'élément avec l'id donné
     */
    public function getAllById(array $IDs)
    {
        if (empty($IDs)) {
            return [];
        }
        $placeholders = implode(',', array_fill(0, count($IDs), '?'));

        try {
            return $this->dbRequestAll(
                "SELECT * FROM `" . $this->tableName . "` WHERE `$this->idName` IN ($placeholders)",
                $IDs
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /*
     * Fonction save
     * paramètres : champs à remplir, valeurs à remplir
     * résultat : Sauvegarde les éléments dans la base de données, retourne la dernière ligne entrée
     */
    public function save(array $fields, array $values)
    {
        $fields = "(" . implode(", ", $fields) . ")";
        $placeholders = implode(',', array_fill(0, count($values), '?'));

        // Insère les valeurs dans les champs correspondants dans une nouvelle entrée en base et retourne le dernier ID inséré
        try {
            return $this->dbRequest(
                "INSERT INTO `" . $this->tableName . "` $fields VALUES ($placeholders)",
                $values,
                true
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
