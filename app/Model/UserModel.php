<?php

namespace HardwareBorrow\Model;

use PDOException;

/*
 * Classe UserModel
 * Gère les requêtes DBB sur la table users
 */

class UserModel extends Model
{
    public function __construct()
    {
        $this->tableName = "user_";
        $this->idName = "id_user";
        parent::__construct();
    }

    /*
     * Fonction getIdByEmail
     * paramètres : email
     * résultat : donne l'id de l'utilisateur
     */
    public function getIdByEmail(string $email)
    {
        $result = $this->dbRequest(
            "SELECT `" . $this->idName . "` FROM `" . $this->tableName . "` WHERE email = ?",
            [$email]
        );

        return is_array($result) && isset($result[$this->idName]) ? (int)$result[$this->idName] : null;
    }

    /*
     * Fonction getPasswordByEmail
     * paramètres : email
     * résultat : mot de passe lié à l'email
     */
    public function getPasswordByEmail(string $email)
    {
        return $this->dbRequest(
            "SELECT password_hash FROM " . $this->tableName . " WHERE email = ?",
            [$email]
        ) ?? null;
    }

    /*
     * Fonction getEmailByID
     * paramètres : id utilisateur
     * résultat : email lié à l'id utilisateur
     */
    public function getEmailByID(int $userID)
    {
        return $this->dbRequest(
            "SELECT email FROM " . $this->tableName . " WHERE id_user = ?",
            [$userID]
        );
    }

    /*
     * Fonction isEmailUsed
     * paramètres : un email
     * résultat : true - si l'email a été trouvé dans la base de données
     *            false - si l'email n'existe pas dans la base de données
     */
    public function isEmailUsed(string $email)
    {
        // Cherche l'utilisateur par email avec requête paramétrée
        $result = $this->dbRequest(
            "SELECT password_hash FROM " . $this->tableName . " WHERE email = ?",
            [$email]
        );

        return ($result !== null) ? true : false;
    }

    /**
     * Fonction isAdmin
     * @param int $id id utilisateur
     * @return bool true si l'utilisateur est admin, false sinon
     */
    public function isAdmin(int $id): bool
    {
        $result = $this->dbRequest(
            "SELECT `role` FROM " . $this->tableName . " WHERE id_user = ?",
            [$id]
        );

        return is_array($result) && isset($result['role'])
            ? $result['role'] === 'admin'
            : false;
    }

    /**
     * Fonction isCurrentAdmin
     * @return bool true si l'utilisateur connecté est admin
     */
    public function isCurrentAdmin(): bool
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        $isAdmin = $this->isAdmin($_SESSION['user_id']);

        if ($isAdmin === true) {
            $_SESSION['is_admin'] = true;
        } else {
            $_SESSION['is_admin'] = false;
        }

        return $isAdmin;
    }

    /**
     * Fonction registerUser
     * paramètres : le nom d'utilisateur, email et mot de passe donnés par l'utilisateur
     */
    public function registerUser(string $first_name, string $last_name, string $email, string $password)
    {
        // Hachage du mot de passe
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->save(['first_name', 'last_name', 'email', 'password_hash'], [$first_name, $last_name, $email, $hash]);
    }

    /**
     * Fonction loginCheck
     * paramètres : l'email et le mot de passe entrés par l'utilisateur
     * résultat : true - si l'email et le mot de passe correspondent à un compte existant
     *            false - si l'email n'est pas trouvé ou si le mot de passe associé à l'email ne correspond pas
     */
    public function loginCheck(string $email, string $password)
    {
        // Cherche un mot de passe correspondant à l'email
        $user = $this->getPasswordByEmail($email);

        // Si rien n'est trouvé, retourne false
        if ($user === null) {
            return false;
        }

        // Le résultat doit être un tableau contenant la clé 'password'
        if (!is_array($user) || !isset($user['password_hash'])) {
            return false;
        }

        // Compare le mot de passe clair entré par l'utilisateur avec le hash en base
        if (password_verify($password, $user['password_hash'])) {
            return true;
        }

        return false;
    }

    /**
     * Fonction delete
     * paramètre : ID de l'utilisateur
     * résultat : supprime le compte de l'utilisateur ainsi que ses emprunts
     *            true si succès, false sinon
     */
    public function delete(int $userID)
    {
        try {
            $this->dbConnector->beginTransaction();

            $reservationIds = $this->dbRequestAll(
                "SELECT id_reservation FROM reservation WHERE id_user = ?",
                [intval($userID)]
            );

            $this->dbRequest(
                "DELETE FROM reservation_items WHERE id_reservation = ?",
                [($reservationIds)]
            );

            $this->dbRequest(
                "DELETE FROM reservation WHERE id_user = ?",
                [intval($userID)]
            );

            $this->dbRequest(
                "DELETE FROM " . $this->tableName . " WHERE id_user = ?",
                [intval($userID)]
            );

            $this->dbConnector->commit();

            return true;
        } catch (PDOException $e) {
            $this->dbConnector->rollBack();
            error_log($e->getMessage());

            return false;
        }
    }
}
