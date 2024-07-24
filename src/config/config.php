<?php
// config.php

// Set up general error logging
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Function to log errors to specific files
function log_error($message, $file) {
    error_log($message . "\n", 3, __DIR__ . '/logs/' . $file);
}

// Load Composer's autoload file
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Define database connection settings
// You may not need to define this here if it's only used in db_config.php
// Define session settings
// You may not need to define this here if it's only used in session_config.php
?>
