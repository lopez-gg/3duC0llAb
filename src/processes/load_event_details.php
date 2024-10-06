<?php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/session_config.php';

$eventId = $_GET['event_id']; // Assuming this is passed as a GET parameter

try {
    $stmt = $pdo->prepare("
        SELECT id, title, description, event_date, end_date, event_type
        FROM events
        WHERE id = :event_id
    ");

    $stmt->execute(['event_id' => $eventId]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($event);

} catch (PDOException $e) {
    log_error('Error loading event details: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode([]);
}
?>
