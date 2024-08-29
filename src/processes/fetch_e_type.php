<?php
require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

try {
    $query = "
        SELECT events.*, event_types.color 
        FROM events 
        JOIN event_types ON events.event_type = event_types.type
        WHERE /* your conditions, like filtering by date, year range, etc. */
    ";
    $events = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error fetching events: ' . $e->getMessage());
    $events = [];
}
?>