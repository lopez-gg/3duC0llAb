<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

$id = $_POST['id'];

$_SESSION['verification_message'] = 'Are you sure you want to delete this event?';
header('Location: handle_events.php');

try {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: ../../../public/admin/handle_events.php");
} catch (Exception $e) {
    log_error('Error archiving event: ' . $e->getMessage(), 'db_errors.txt');
    echo "Error archiving event.";
}
?>
