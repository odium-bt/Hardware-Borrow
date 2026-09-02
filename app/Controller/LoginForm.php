<?php

namespace HardwareBorrow\Controller;

use HardwareBorrow\Model\UserModel;
/*
 * Classe LoginForm
 * Gère la page de confirmation de suppression du compte
 */

class LoginForm
{
    public $errors = [];
    protected string $email;
    protected string $password;
    protected int $counter = 0;

    // Données du compteur
    protected float $startTime = 0;
    protected float $endTime = 0;
    protected float $elapsedTime = 0;

    protected UserModel $user;

    public function __construct()
    {

        $this->user = new UserModel;


        // Validation du POST (trouve les erreurs et ajoute les messages dans le tableau $errors)
        if (!empty($_POST)) {
            foreach ($_POST as $key => $post_element) {
                switch ($key) {
                    case 'email':
                        $this->email = strtolower(trim($_POST['email'])) ?? null;
                        break;
                    case 'password':
                        $this->password = trim($_POST['password']) ?? null;
                        break;
                }
            }

            // Email
            if (!$this->email) {
                $this->errors['email'] = "Requis";
            }
            // Mot de passe
            if (!$this->password) {
                $this->errors['password'] = "Requis";
            }


            if (!$this->user->loginCheck($this->email, $this->password)) {
                $this->errors['failure'] = "L'email ou le mot de passe que vous avez entrés sont erronés";
                // Incrémente le compteur d'erreurs de connexion
                $this->incrementCounter();
            } else {
                $this->elapsedTime = 0;
                $this->counter = 0;
            };
        }
    }

    /**
     * Fonction incrementCounter |
     * Si le compteur d'erreurs est à 10 ou +, bloque l'utilisateur avec startTimer 
     */
    private function incrementCounter()
    {
        if ($this->counter >= 5) {
            $this->errors['failure'] = "Trop de tentatives successives, merci d'attendre 30 minutes avant de réessayer";
            if ($this->elapsedTime <= 0) {
                $this->startTimer();
            }
        } else {
            $this->counter += 1;
        }
    }

    /**
     * Fonction startTimer a pour but de limiter la fréquence à laquelle un utilisateur peut tenter de se connecter
     * paramètres : /
     * résultat : commence un décompte; après 30 minutes la variable counter retourne à 0
     */
    protected function startTimer()
    {
        $this->startTime = microtime(true);
        while ($this->elapsedTime <= 1800) {
            $this->endTime = microtime(true);
            $this->elapsedTime = $this->endTime - $this->startTime;
            usleep(1000000); // waits 1 second
        }
        $this->elapsedTime = 0;
        $this->counter = 0;
    }
}
