<?php

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$recipient_id = $_SESSION['user_id']; // Current logged-in user (the recipient)

// Retrieve JSON input from the request body
$input = json_decode(file_get_contents('php://input'), true);

// Extract senderId from the JSON input
$sender_id = isset($input['senderId']) ? $input['senderId'] : null;

if ($sender_id === null) {
    echo json_encode(['error' => 'Sender ID not provided']);
    exit;
}

try {
    // Update all unread messages to mark them as "read"
    $updateQuery = "
        UPDATE messages 
        SET read_at = NOW() 
        WHERE recipient_id = :recipient_id 
        AND sender_id = :sender_id
        AND read_at IS NULL
    ";

    $stmt = $pdo->prepare($updateQuery);
    $stmt->bindParam(':recipient_id', $recipient_id, PDO::PARAM_INT);
    $stmt->bindParam(':sender_id', $sender_id, PDO::PARAM_INT);
    
    $stmt->execute();

    // Return success message
    echo json_encode(['status' => 'Messages marked as read']);
} catch (PDOException $e) {
    // Log the error and return an error response
    error_log('Database query failed: ' . $e->getMessage(), 3, 'db_errors.log');
    echo json_encode(['error' => 'Failed to mark messages as read']);
}
