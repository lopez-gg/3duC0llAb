<?php
require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($event_id <= 0) {
    echo json_encode(['error' => 'Invalid event ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, title, description, event_date AS start, end_date AS end FROM events WHERE id = :event_id");
    $stmt->execute(['event_id' => $event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($event) {
        echo json_encode($event);
    } else {
        echo json_encode(['error' => 'Event not found']);
    }
} catch (Exception $e) {
    log_error('Error fetching event details: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['error' => 'Error fetching event details']);
}
?>
