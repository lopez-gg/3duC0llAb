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

    if (empty($events)) {
        $message = "No events found for tomorrow.<br>";
    } else {
        foreach ($events as $event) {
            $event_id = $event['id'];
            $title = $event['title'];
            $start_date = $event['event_date'];
            $end_date = $event['end_date'] ?? $start_date; // Default to start_date if no end_date is provided

            $message = "Tomorrow's event: '{$title}' ({$start_date} to {$end_date})";

            // Check if a notification with the same event_id already exists
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM notifications 
                WHERE event_id = :event_id
            ");
            $stmt->execute([
                'event_id' => $event_id
            ]);

            $notificationExists = $stmt->fetchColumn() > 0;

            if (!$notificationExists) {
                // Insert a new notification for the event
                $stmt = $pdo->prepare("
                    INSERT INTO notifications (event_id, notif_content, type, event_start_date, event_end_date, created_at) 
                    VALUES (:event_id, :message, 'calendar_event', :start_date, :end_date, NOW())
                ");
                $stmt->execute([
                    'event_id' => $event_id,
                    'message' => $message,
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]);
                // Notification inserted
            }
        }
    }

    // Mark notifications for past events as 'read' where the event date has passed
    $stmt = $pdo->prepare("
        UPDATE notifications 
        SET read_at = NOW() 
        WHERE type = 'calendar_event' 
        AND EXISTS (
            SELECT 1 FROM events 
            WHERE events.id = notifications.event_id
            AND events.event_date < CURDATE()
        )
    ");
    $stmt->execute();

} catch (PDOException $e) {
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
}
?>
