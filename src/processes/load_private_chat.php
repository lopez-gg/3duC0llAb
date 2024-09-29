<?php
//load_private_chat
// Load required configuration files
require_once __DIR__ . '/../config/config.php';        // Load general configuration
require_once __DIR__ . '/../config/db_config.php';     // Load database configuration
require_once __DIR__ . '/../config/session_config.php'; // Load session configuration

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in.']));
}

// Fetch messages between logged-in user and selected user
try {
    $current_user_id = $_SESSION['user_id']; // Logged-in user
    $selected_user_id = $_POST['userId'];    // Selected user for private chat

    // Prepare SQL query to fetch messages between the two users
    $sql = "SELECT m.*, u1.username AS sender_username, u2.username AS recipient_username
            FROM messages AS m
            JOIN users AS u1 ON m.sender_id = u1.id
            JOIN users AS u2 ON m.recipient_id = u2.id
            WHERE (m.sender_id = :currentUserId AND m.recipient_id = :selectedUserId)
               OR (m.sender_id = :selectedUserId AND m.recipient_id = :currentUserId)
            ORDER BY m.created_at ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'currentUserId' => $current_user_id,
        'selectedUserId' => $selected_user_id
    ]);

    // Fetch the messages
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add an 'is_mine' flag to each message
    foreach ($messages as &$message) {
        $message['is_mine'] = ($message['sender_id'] == $current_user_id); // Add flag
    }

    // Return the messages
    header('Content-Type: application/json');
    if (empty($messages)) {
        echo json_encode(['message' => 'You have not initiated a conversation with this member yet.']);
    } else {
        echo json_encode($messages); // No need to return current_user_id
    }
} catch (PDOException $e) {
    // Log error if needed
    log_error('Query failed: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['error' => 'An error occurred while fetching the chat history.']);
}
?>
