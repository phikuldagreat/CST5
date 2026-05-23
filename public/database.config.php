<?php
//Database configuration
//used Composer to install phpdotenv and read the .env file 
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
// Using safeLoad() instead of load() so it won't crash if .env doesn't exist
$dotenv->safeLoad();

$SERVER_NAME = $_ENV['DB_HOST'];
$USERNAME = $_ENV['DB_USER'];
$PASSWORD = $_ENV['DB_PASSWORD'];
$DB_NAME = $_ENV['DB_NAME'];
$DB_PORT = $_ENV['DB_PORT'];