<?php
// check_upcoming_events.php

require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

try {
    // Get tomorrow's date
    $today = new DateTime();
    $tomorrow = clone $today;
    $tomorrow->modify('+1 day');
    $tomorrow_date = $tomorrow->format('Y-m-d');

    // echo "Checking events for: " . $tomorrow_date . "<br>";

    // Prepare and execute the query to fetch events happening tomorrow
    $stmt = $pdo->prepare("SELECT title FROM events WHERE event_date = :tomorrow_date");
    $stmt->execute(['tomorrow_date' => $tomorrow_date]);

    // Fetch all events happening tomorrow
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($events)) {
        echo "No events found for tomorrow.<br>";
    } else {
        foreach ($events as $event) {
            $title = $event['title'];
            $message = "Tomorrow's event: '{$title}'.";

            // Check if a notification with the same title and date already exists
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM notifications 
                WHERE notif_content = :message AND type = 'calendar_event' AND DATE(created_at) = :tomorrow_date
            ");
            $stmt->execute([
                'message' => $message,
                'tomorrow_date' => $tomorrow_date
            ]);
            
            $notificationExists = $stmt->fetchColumn() > 0;

            if (!$notificationExists) {
                // Insert a general notification if it does not already exist
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (notif_content, type, created_at) 
                    VALUES (:message, 'calendar_event', NOW())
                ");
                $stmt->execute(['message' => $message]);
                // echo "Notification inserted: " . $message . "<br>";
            } else {
                // echo "Notification already exists for: " . $message . "<br>";
            }
        }
    }
} catch (PDOException $e) {
    // echo "Error: " . $e->getMessage() . "<br>";
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
}
?>
