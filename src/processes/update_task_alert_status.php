<?php
// update_task_alert_status.php

require_once __DIR__ . '/../config/db_config.php';  // Database connection
require_once __DIR__ . '/../config/session_config.php';  // Session management

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or show an error message
    header('Location: ../../public/login.php');
    exit;
}

$currentUserId = $_SESSION['user_id'];
$taskId = $_POST['rchived_task_id']; // Fixed missing semicolon

// Query to fetch account type from users table
try {
    $stmt = $pdo->prepare("SELECT accType FROM users WHERE id = :userId");
    $stmt->execute(['userId' => $currentUserId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $accountType = $user['accType']; // Fetch the account type
    } else {
        // Handle case where user is not found, if necessary
        header('Location: ../../public/error_page.php?message=User not found.');
        exit;
    }
} catch (PDOException $e) {
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    header('Location: ../../public/error_page.php?message=Failed to retrieve account type.');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Update alert_status to 'read' for the specified archived task
        $stmt = $pdo->prepare("
            UPDATE archived_tasks 
            SET alert_status = 'read' 
            WHERE id = :taskId 
            AND assignedTo = :curUserId
        ");
        $stmt->execute(['taskId' => $taskId, 'curUserId' => $currentUserId]);

        // Redirect back with a success message
        if($accountType === 'ADMIN'){
            header('Location: ../../public/admin/dashboard.php');
        }else{
            header('Location: ../../public/user/dashboard.php');
        }
        exit;

    } catch (PDOException $e) {
        log_error('Database update failed: ' . $e->getMessage(), 'db_errors.txt');
        // Redirect back with an error message
        // header('Location: ../../public/error_page.php?message=Failed to update alert status.');
        exit;
    }
} else {
    // Redirect if the request method is not POST
    // header('Location: ../../public/error_page.php?message=Invalid request method.');
    exit;
}
?>
