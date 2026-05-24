<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if (is_file($file)) {
    return false; // serve the file as-is
}

// route to index.php for everything else
include __DIR__ . '/index.php';