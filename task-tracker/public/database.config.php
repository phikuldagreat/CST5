<?php
//Database configuration
//used Composer to install PHP dependencies
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
// Using safeLoad() instead of load() so it won't crash if .env doesn't exist
$dotenv->safeLoad();

$SERVER_NAME = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
$USERNAME = $_ENV['DB_USER'] ?? getenv('DB_USER');
$PASSWORD = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
$DB_NAME = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
$DB_PORT = (int) ($_ENV['DB_PORT'] ?? getenv('DB_PORT'));