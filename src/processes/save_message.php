<?php
// save_message.php
require_once __DIR__ . '/../config/db_config.php'; // Load database configuration
require_once __DIR__ . '/../config/session_config.php'; // Load session configuration

// Ensure the user is logged in before proceeding
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in.']));
}

$sender_id = $_SESSION['user_id']; // Get the logged-in user ID
$recipient_id = $_POST['recipientId']; // ID of the user to send the message to
$message = $_POST['message']; // The message content

try {
    // Prepare and execute the SQL statement to save the message
    $sql = "INSERT INTO messages (sender_id, recipient_id, message_text) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql); // Change $conn to $pdo

    // Bind parameters
    $stmt->bindParam(1, $sender_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $recipient_id, PDO::PARAM_INT);
    $stmt->bindParam(3, $message, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'messageId' => $pdo->lastInsertId()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save the message.']);
    }

} catch (PDOException $e) {
    // Log the error
    log_error('Message saving error: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['status' => 'error', 'message' => 'An error occurred while saving the message.']);
}

?>