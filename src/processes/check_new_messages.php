<?php
// check_upcoming_events.php

require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit;
}

$recipient_id = $_SESSION['user_id']; 
$unreadCount = 0;

try {
    // Prepare the query to count unread messages
    $unreadQuery = "
        SELECT COUNT(*) AS unread_count 
        FROM messages 
        WHERE recipient_id = :recipient_id 
        AND read_at IS NULL
    ";

    $stmt = $pdo->prepare($unreadQuery);
    $stmt->bindParam(':recipient_id', $recipient_id, PDO::PARAM_INT);
    $stmt->execute();
    $unreadCount = $stmt->fetchColumn();
    echo json_encode(['unread_count' => (int)$unreadCount]);

} catch (PDOException $e) {
    error_log('Database query failed: ' . $e->getMessage(), 3, 'db_errors.log');
    echo json_encode(['error' => 'Error fetching unread message count']);
}
?>
