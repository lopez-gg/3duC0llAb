<?php
// fetch_events.php

require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

try {
    // Prepare and execute the query to fetch events ordered by event_date and end_date in descending order
    $stmt = $pdo->prepare("SELECT * FROM events ORDER BY event_date DESC, end_date DESC");
    $stmt->execute();
    
    // Fetch all events
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log error to file using the function from config.php
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    $events = [];
}

// Return the events array
return $events;
?>

