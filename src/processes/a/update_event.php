<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';

$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$start = $_POST['start'];
$end = $_POST['end'];

try {
    $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, end_date = ? WHERE id = ?");
    $stmt->execute([$title, $description, $start, $end, $id]);
    header("Location: ../../../public/admin/handle_events.php");
} catch (Exception $e) {
    log_error('Error updating event: ' . $e->getMessage(), 'db_errors.txt');
    echo "Error updating event.";
}
?>
