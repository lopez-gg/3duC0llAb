<?php
// fetch_notifications.php

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../config/session_config.php';

header('Content-Type: application/json'); // Ensure the content type is JSON

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Get the user_id from the session

try {
    // Prepare and execute the query to fetch both user-specific and general notifications
    $query = "
        SELECT * 
        FROM notifications 
        WHERE (user_id = :user_id OR user_id IS NULL) 
          AND read_at IS NULL
        ORDER BY created_at DESC
    ";

    $stmt = $pdo->prepare($query);
    
    if ($user_id) {
        // Execute with user_id
        $stmt->execute(['user_id' => $user_id]);
    } else {
        // Execute without user_id (if no user_id in session, which shouldn't normally happen if users are logged in)
        $stmt->execute();
    }
    
    // Fetch all relevant notifications
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if no notifications are found
    if (empty($notifications)) {
        $notifications = ['message' => 'No recent notifications'];
    }
} catch (PDOException $e) {
    // Log error to file using the function from config.php
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    $notifications = ['message' => 'Error fetching notifications'];
}

// Return the notifications array as JSON
echo json_encode(['notifications' => $notifications]);
?>
