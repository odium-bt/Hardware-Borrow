<?php

namespace HardwareBorrow\Controller;

use HardwareBorrow\Model\UserModel;
/*
 * Contrôle de qualité du formulaire d'inscription
 * Filtre tous les champs avant de refuser ou valider l'inscription d'un nouvel utilisateur
 */

class Register
{
    public $errors = [];
    protected string $first_name = "";
    protected string $last_name = "";
    protected string $email = "";
    protected string $emailConfirm = "";
    protected string $password = "";
    protected string $passwordConfirm = "";

    public function __construct()
    {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $post_element) {
                switch ($key) {
                    case 'first_name':
                        $this->first_name = trim($_POST['first_name']);
                        break;
                    case 'last_name':
                        $this->last_name = trim($_POST['last_name']);
                        break;
                    case 'email':
                        $this->email = strtolower(trim($_POST['email']));
                        break;
                    case 'email-confirm':
                        $this->emailConfirm = strtolower(trim($_POST['email-confirm']));
                        break;
                    case 'password':
                        $this->password = trim($_POST['password']);
                        break;
                    case 'password-confirm':
                        $this->passwordConfirm = trim($_POST['password-confirm']);
                }
            }

            // Contrôle de qualité (trouve les erreurs et ajoute les messages dans le tableau $errors)
            // Prénom
            if ($this->first_name === "") {
                $this->errors['first_name'] = "Requis";
            } else  if (strlen($this->first_name) < 3) {
                $this->errors['first_name'] = "Au moins 3 caractères";
            } else if (strlen($this->first_name) > 100) {
                $this->errors['first_name'] = "Moins de 100 caractères";
            }
            // Nom
            if ($this->last_name === "") {
                $this->errors['last_name'] = "Requis";
            } else  if (strlen($this->last_name) < 3) {
                $this->errors['last_name'] = "Au moins 3 caractères";
            } else if (strlen($this->last_name) > 100) {
                $this->errors['last_name'] = "Moins de 100 caractères";
            }
            // Email
            if ($this->email === "") {
                $this->errors['email'] = "Requis";
            } else if (strlen(($this->email)) > 254) {
                $this->errors['email'] = "L'adresse entrée est trop longue";
            } else if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
                $this->errors['email'] = "Veuillez entrer une adresse email valide";
            }
            // Confirmation d'email
            if ($this->emailConfirm === "") {
                $this->errors['email-confirm'] = "Requis";
            } else if ($this->email != $this->emailConfirm) {
                $this->errors['email-confirm'] = "Les email entrés ne sont pas identiques";
            }
            // Mot de passe
            if ($this->password === "") {
                $this->errors['password'] = "Requis";
            } else if (strlen($this->password) < 8) {
                $this->errors['password'] = "Veuillez choisir un mot de passe de plus de 8 caractères";
            } else if (strlen($this->password) >= 128) {
                $this->errors['password'] = "Veuillez choisir un mot de passe de moins de 128 caractères";
            }
            // Confirmation du mot de passe
            if ($this->passwordConfirm === "") {
                $this->errors['password-confirm'] = "Requis";
            } else if ($this->password != $this->passwordConfirm) {
                $this->errors['password-confirm'] = "Les mots de passe entrés ne sont pas identiques";
            }
        }

        // ============ Sortie
        // Si $_POST est vide (début) ou qu'il y a des erreurs, affiche le formulaire
        if (empty($_POST) || !empty($this->errors)) {
            require ROOT . '/app/View/inscription_view.php';
        }
        // Sinon $_POST est rempli sans erreurs, effectue une dernière vérification
        else {
            if (!isset($user)) {
                $user = new UserModel;
            }

            // Vérifie que l'adresse email ne soit pas déjà utilisée
            if ($user->isEmailUsed($this->email) === false) {
                $user->registerUser($this->first_name, $this->last_name, $this->email, $this->password); // envoie les infos utilisateur sur le modèle
                require ROOT . '/app/View/inscription-success_view.php'; // affiche page succès
            } else {
                // Affiche une erreur dans le formulaire d'inscription
                $this->errors['email'] = "Cette adresse existe déjà";
                require ROOT . '/app/View/inscription_view.php'; // Retour au formulaire
            }
        }
        // ============
    }
}
