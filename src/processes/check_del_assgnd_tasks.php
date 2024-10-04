<?php 
// check_del_assigned_tasks.php

require_once __DIR__ . '/../config/db_config.php';  // Database connection
require_once __DIR__ . '/../config/session_config.php';  // Session management

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
}

$currentUserId = $_SESSION['user_id']; // Get current user's ID from session
$tasks = []; // Initialize tasks array

try {
    // Prepare the query to check for pending archived tasks assigned to the current user
    // Joining users table to get assignedBy and assignedTo details
    $stmt = $pdo->prepare("
        SELECT archived_tasks.*, 
               assignedByUser.firstname AS assignedByFirstName, 
               assignedByUser.lastname AS assignedByLastName,
               assignedToUser.firstname AS assignedToFirstName, 
               assignedToUser.lastname AS assignedToLastName
        FROM archived_tasks
        JOIN users AS assignedByUser ON archived_tasks.assignedBy = assignedByUser.id
        JOIN users AS assignedToUser ON archived_tasks.assignedTo = assignedToUser.id
        WHERE archived_tasks.assignedTo = :assignedTo 
        AND archived_tasks.alert_status = 'pending'
        AND archived_tasks.taskType = 'assigned'
        AND archived_tasks.status = 'archived'
    ");
    $stmt->execute(['assignedTo' => $currentUserId]);
    
    // Fetch all matching records
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log any errors
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
}
?>
