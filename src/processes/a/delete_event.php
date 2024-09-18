<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

$id = $_POST['id'] ?? null;

if (empty($id)) {
    $_SESSION['success_message'] = 'An error occured. Please try again.';
    log_error('Event ID is empty: ' . $e->getMessage(), 'db_errors.txt');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    if ($stmt->execute([$id])) {
        $_SESSION['success_message'] = 'Event successfully deleted.';
    } else {
        $_SESSION['success_message'] = 'Failed to delete event.';
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed deleting event.']);
    log_error('Error deleting event: ' . $e->getMessage(), 'db_errors.txt');
    $_SESSION['success_message'] = 'Error deleting event.';
}

exit;
?>
