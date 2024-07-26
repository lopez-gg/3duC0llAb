<?php
// db_config.php

require_once __DIR__ . '/config.php'; // Includes general configuration

// Database credentials from .env
$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log error to file using the function from config.php
    log_error('Connection failed: ' . $e->getMessage(), 'db_errors.txt');
    echo 'Connection failed. Please try again later.';
    exit;
}
?>
