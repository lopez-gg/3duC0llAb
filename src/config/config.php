<?php
// config.php

// Set up general error logging
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../logs/error.log');


// Function to log errors to specific files
function log_error($message, $file) {
    error_log($message . "\n", 3, __DIR__ . '/../../logs/' . $file);
}

// Load Composer's autoload file
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Other general configurations (if any)

// Utility functions for task urgency
function getUrgencyLabel($tag) {
    switch ($tag) {
        case 'UI':
            return 'Urgent & Important';
        case 'UNI':
            return 'Urgent but Not Important';
        case 'NUI':
            return 'Not Urgent but Important';
        case 'NUNI':
            return 'Not Urgent & Not Important';
        default:
            return 'Normal';
    }
}

function getUrgencyColor($tag) {
    switch ($tag) {
        case 'UI':
            return 'red';
        case 'UNI':
            return 'orange';
        case 'NUI':
            return 'yellow';
        case 'NUNI':
            return 'blue';
        default:
            return 'white';
    }
}

function getCurrentYearRange() {
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    return "$currentYear-$nextYear";
}

?>

