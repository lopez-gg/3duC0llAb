<?php
require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

try {
    $stmt = $pdo->query("SELECT id, title, description, event_date as start, end_date as end FROM events");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($events);
} catch (Exception $e) {
    log_error('Error fetching events: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode([]);
}
?>
