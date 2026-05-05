<?php


require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env from project root
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

/* -------------------------
   APP SETTINGS
--------------------------*/
define('APP_NAME', $_ENV['APP_NAME']);
define('APP_ENV', $_ENV['APP_ENV']);
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN));

/* -------------------------
   DATABASE SETTINGS
--------------------------*/
define('DB_HOST', $_ENV['DB_HOST']);
define('DB_NAME', $_ENV['DB_NAME']);
define('DB_USER', $_ENV['DB_USER']);
define('DB_PASS', $_ENV['DB_PASS']);

/* -------------------------
   ERROR HANDLING
--------------------------*/
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
}