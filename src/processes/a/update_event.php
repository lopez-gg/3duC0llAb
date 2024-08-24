<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

$id = $_POST['id'] ?? null;

if (empty($id)) {
    $_SESSION['success_message'] = 'Something went wrong. Please try again.';
    header("Location: ../../../public/admin/handle_events.php");
    exit;
}

$id = $_POST['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$start = $_POST['start'];
$end = $_POST['end'];
$type = $_POST['type'];

try {
    $stmt = $pdo->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, end_date = ?, event_type = ? WHERE id = ?");
    $stmt->execute([$title, $description, $start, $end, $type, $id]);
    $_SESSION['success_message'] = 'Event successfully updated.';
    header("Location: ../../../public/admin/handle_events.php");
} catch (Exception $e) {
    $_SESSION['success_message'] = 'Failed updating event.';
    log_error('Error updating event: ' . $e->getMessage(), 'db_errors.txt');
}


?>
