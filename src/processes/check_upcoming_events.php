<?php
// check_upcoming_events.php

require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

$user_id = $_SESSION['user_id']; 


try {
    // Get the current date and the date for tomorrow
    $today = new DateTime();
    $tomorrow = clone $today;
    $tomorrow->modify('+1 day');
    $tomorrow_date = $tomorrow->format('Y-m-d');

    // Prepare and execute the query to fetch events happening tomorrow
    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_date = :tomorrow_date");
    $stmt->execute(['tomorrow_date' => $tomorrow_date]);
    
    // Fetch all events happening tomorrow
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($events as $event) {
        $user_id = $event['user_id'];
        $message = "Tomorrow's event: '{$event['title']}'.";
        
        // Insert a notification for each event happening tomorrow
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (:user_id, :message)");
        $stmt->execute(['user_id' => $user_id, 'message' => $message]);
    }
} catch (PDOException $e) {
    // Log error to file using the function from config.php
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
}
?>
