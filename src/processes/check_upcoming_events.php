<?php
// check_upcoming_events.php

require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

try {
    $today = new DateTime();
    $tomorrow = clone $today;
    $tomorrow->modify('+1 day');
    $tomorrow_date = $tomorrow->format('Y-m-d');

    // Prepare and execute the query to fetch events happening tomorrow
    $stmt = $pdo->prepare("SELECT id, title, event_date, end_date FROM events WHERE event_date = :tomorrow_date");
    $stmt->execute(['tomorrow_date' => $tomorrow_date]);

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $messages = [];

    if (empty($events)) {
        $message[] = "No events found for tomorrow.<br>";
    } else {
        foreach ($events as $event) {
            $event_id = $event['id'];
            $title = $event['title'];
            $start_date = $event['event_date'];
            $end_date = $event['end_date'] ?? $start_date; // Default to start_date if no end_date is provided

            $message = "Tomorrow's event: '{$title}' ({$start_date} to {$end_date})";
            $messages[] = $message;

            // Check if a notification with the same event_id already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE event_id = :event_id");
            $stmt->execute(['event_id' => $event_id]);

            if ($stmt->fetchColumn() === 0) {
                // Insert a new notification for the event
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (event_id, notif_content, type, created_at) 
                    VALUES (:event_id, :message, 'calendar_event', NOW())
                ");
                $stmt->execute([
                    'event_id' => $event_id,
                    'message' => $message
                ]);
            }
        }
    }


} catch (PDOException $e) {
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
}
?>
