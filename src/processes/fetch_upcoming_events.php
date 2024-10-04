<?php
// fetch_events.php

require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

try {
    // Get the current month and year
    $currentMonth = date('m'); // Format: 01-12
    $currentYear = date('Y'); // Format: YYYY

    // Prepare and execute the query to fetch events for the current month
    $stmt = $pdo->prepare("
        SELECT * 
        FROM events 
        WHERE MONTH(event_date) = :currentMonth AND YEAR(event_date) = :currentYear 
        ORDER BY event_date DESC, end_date DESC
    ");
    $stmt->execute([
        ':currentMonth' => $currentMonth,
        ':currentYear' => $currentYear
    ]);
    
    // Fetch all events for the current month
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log error to file using the function from config.php
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    $events = [];
}

// Return the fetched events to be used in the front-end
return $events;
?>
