<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';

$id = $_POST['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: handle_events.php");
} catch (Exception $e) {
    log_error('Error archiving event: ' . $e->getMessage(), 'db_errors.txt');
    echo "Error archiving event.";
}
?>
