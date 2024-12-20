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
            return 'white';
        case 'Urgent':
            return 'yellow';
        case 'Important':
            return 'orange';
        case 'Urgent and Important':
            return 'red';
        default:
            return 'white';
    }
}

function getEventTypeColor($tag) {
    switch ($tag) {
        case 'School':
            return 'white';
        case 'Holiday':
            return 'yellow';
        case 'Special':
            return 'orange';
        case 'Others':
            return 'red';
        default:
            return 'white';
    }
}


function getTaskType($task_type) {
    switch($task_type) {
        case 'assigned':
            return $task_type_icon = "bi bi-people-fill";
        case 'private':
            return $task_type_icon = "bi bi-person-fill-lock";
        default:
            return $task_type_icon = "bi bi-question-circle";
    }
}

date_default_timezone_set('Asia/Manila'); 
$currentDateTime = date('l, d/m/Y h:i:s A'); 

function getCurrentYearRange() {
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    return "$currentYear-$nextYear";
}

$dashb = '';
$my_space = '';
$calendr = '';
$gen_forum ='';
$faculty = '';

function getNavState($currNav) {
    switch($currNav){
        case 'dashboard':
            return $dashb = 'active';
        case 'my_space':
            return $my_space = 'active';
        case 'calendar':
            return $calendr = 'active';
        case 'gen_forum':
            return $gen_forum = 'active';
        case 'faculty':
            return $faculty = 'active';
        default:
        return 'none';
    }
}

function add_activity_history ($userID, $subjectID, $activity_message){
    global $pdo;
    try{
         // Insert a notification for the request_owner
        $history_sql = "INSERT INTO activities_history (user_id, subject_id, message, created_at) 
        VALUES (:user_id, :subject_id, :message, NOW())";
   
        $history_stmt = $pdo->prepare($history_sql);
        $history_stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
        $history_stmt->bindParam(':subject_id', $subjectID, PDO::PARAM_INT);
        $history_stmt->bindParam(':message', $activity_message, PDO::PARAM_STR);

        // Execute the notification insert
        $history_stmt->execute();

        


    }catch (PDOException $e) {
        log_error('Query failed: ' . $e->getMessage(), 'db_errors.txt');
        exit;
    }
}

?>

