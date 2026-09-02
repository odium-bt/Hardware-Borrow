<?php
require __DIR__ . '/config/config.php';
require ROOT . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();

use HardwareBorrow\Controller\Route;

if (session_status() === PHP_SESSION_NONE) {
    session_name('session_hardwareborrow');
    session_start();
}

new Route;
