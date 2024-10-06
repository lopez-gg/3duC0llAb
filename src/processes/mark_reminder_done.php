<?php
// mark_reminder_done.php
require_once __DIR__ . '/../config/db_config.php'; // Includes database configuration
require_once __DIR__ . '/../config/session_config.php'; // Includes session


// Assuming the user's ID is stored in the session
$userId = $_SESSION['user_id']; // Adjust if necessary

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['message' => 'User not logged in']);
    exit;
}

// Get the reminder ID from the request
$data = json_decode(file_get_contents('php://input'), true);
$reminderId = $data['reminder_id'];

if (!$reminderId) {
    echo json_encode(['success' => false, 'message' => 'Invalid reminder ID']);
    exit;
}

try {
    // Mark the reminder as done
    $stmt = $pdo->prepare("UPDATE reminders SET status = 'completed', reminded_at = NOW() WHERE id = :reminder_id AND user_id = :user_id");
    $stmt->execute(['reminder_id' => $reminderId, 'user_id' => $userId]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    log_error('Error marking reminder as done: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['success' => false, 'message' => 'Failed to mark reminder as done']);
}
?>
