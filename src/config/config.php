<?php
// config.php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

//Base url
$config = [
    'base_url' => $_ENV['BASE_URL'],
];

// Set up general error logging
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/../logs/error.log');


// Function to log errors to specific files
function log_error($message, $file) {
    error_log($message . "\n", 3, __DIR__ . '/../../logs/' . $file);
}

function getUrgencyColor($tag) {
    switch ($tag) {
        case 'Normal':
            return 'gray';
        case 'Urgent':
            return 'yellow';
        case 'Important':
            return 'orange';
        case 'Urgent and Important':
            return 'red';
        default:
            return 'gray';
    }
}

$currentDateTime = date('l, d/m/Y h:i:s A'); 

function getCurrentYearRange() {
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    return "$currentYear-$nextYear";
}

?>

