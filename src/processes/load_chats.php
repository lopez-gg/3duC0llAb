<?php
// Load required configuration files
require_once __DIR__ . '/../config/config.php';        // Load general configuration
require_once __DIR__ . '/../config/db_config.php';     // Load database configuration
require_once __DIR__ . '/../config/session_config.php'; // Load session configuration

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json'); // Set the content type
    echo json_encode(['error' => 'User not logged in.']);
    exit; // Always exit after sending a response
}

try {
    $user_id = $_SESSION['user_id'];

    // Query to fetch distinct conversations with the most recent message
    $sql = "SELECT 
                u.id AS user_id, 
                u.username AS chat_username,
                u.gradeLevel, 
                u.section,
                m.message_text, 
                m.created_at
            FROM users u
            JOIN (
                SELECT 
                    m1.sender_id, 
                    m1.recipient_id,
                    m1.message_text,
                    m1.created_at
                FROM messages m1
                INNER JOIN (
                    SELECT 
                        GREATEST(m.sender_id, m.recipient_id) AS user_id,
                        LEAST(m.sender_id, m.recipient_id) AS other_user_id,
                        MAX(m.created_at) AS last_message_time
                    FROM messages m
                    WHERE m.sender_id = :userId OR m.recipient_id = :userId
                    GROUP BY GREATEST(m.sender_id, m.recipient_id), LEAST(m.sender_id, m.recipient_id)
                ) last_messages ON 
                    GREATEST(m1.sender_id, m1.recipient_id) = last_messages.user_id 
                    AND LEAST(m1.sender_id, m1.recipient_id) = last_messages.other_user_id
                    AND m1.created_at = last_messages.last_message_time
            ) m ON (m.sender_id = u.id OR m.recipient_id = u.id)
            WHERE u.id != :userId
            ORDER BY m.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $user_id]);

    // Fetch all distinct conversations with the most recent message
    $chatUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');

    // Check if any chats exist
    if (empty($chatUsers)) {
        echo json_encode(['message' => 'No messages yet.']);
    } else {
        // Return the conversation list as JSON
        echo json_encode($chatUsers);
    }
} catch (PDOException $e) {
    log_error('Query failed: ' . $e->getMessage(), 'db_errors.txt');
    header('Content-Type: application/json');
    echo json_encode(['error' => 'An error occurred while loading conversations.']);
    exit;
}
?>
