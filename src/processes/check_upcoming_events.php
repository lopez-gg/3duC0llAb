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
    $stmt = $pdo->prepare("SELECT title FROM events WHERE event_date = :tomorrow_date");
    $stmt->execute(['tomorrow_date' => $tomorrow_date]);

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($events)) {
        echo "No events found for tomorrow.<br>";
    } else {
        foreach ($events as $event) {
            $title = $event['title'];
            $message = "Tomorrow's event: '{$title}'.";

            // Check if a notification with the same title and date already exists (ignoring time)
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM notifications 
                WHERE notif_content = :message 
                AND type = 'calendar_event' 
                AND DATE(created_at) = :today_date
            ");
            $stmt->execute([
                'message' => $message,
                'today_date' => $today->format('Y-m-d')
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

    // Mark notifications for past events as read
    $stmt = $pdo->prepare("
        UPDATE notifications 
        SET read_at = NOW() 
        WHERE type = 'calendar_event' 
          AND notif_content LIKE '%event:%'
          AND EXISTS (
              SELECT 1 FROM events 
              WHERE events.title = SUBSTRING_INDEX(notif_content, ':', -1) 
                AND events.event_date < CURDATE()
          )
    ");
    $stmt->execute();

} catch (PDOException $e) {
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
}
?>
