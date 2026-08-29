<?php
require __DIR__ . '/config/config.php';
require ROOT . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();

use CoteInfo\Controller\Route;

if (session_status() === PHP_SESSION_NONE) {
    session_name('session_coteinfo'); // Nom de la session
    session_start();
}

new Route;
