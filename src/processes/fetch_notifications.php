<?php
// fetch_notifications.php

require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

session_start();

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session

try {
    // Fetch notifications for the logged-in user
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = :user_id AND status = 'unread'");
    $stmt->execute(['user_id' => $user_id]);
    
    // Fetch all unread notifications
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log error to file using the function from config.php
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    $notifications = [];
}

// Return the notifications array as JSON
echo json_encode(['notifications' => $notifications]);
?>
