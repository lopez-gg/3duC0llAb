<?php
// check_due_tasks.php

require_once __DIR__ . '/../config/config.php'; // General configuration
require_once __DIR__ . '/../config/db_config.php'; // Database configuration
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit;
}

// Set the threshold for nearing due dates (e.g., tasks due in the next 3 days)
$thresholdDays = 3;
$currentDateTime = new DateTime();
$currentDate = $currentDateTime->format('Y-m-d'); // Current date only (ignore time)
$dueDateThreshold = $currentDateTime->add(new DateInterval('P' . $thresholdDays . 'D'))->format('Y-m-d');

// Fetch tasks due in the next 3 days (and not past)
try {
    $stmt = $pdo->prepare("
        SELECT * FROM tasks 
        WHERE due_date >= :current_date 
        AND due_date <= :due_date_threshold 
        AND progress != 'completed'
    ");
    $stmt->execute([
        'current_date' => $currentDate,
        'due_date_threshold' => $dueDateThreshold
    ]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($tasks as $task) {
        if (empty($task['due_date'])) {
            continue; // Skip tasks without a due date
        }

        // Compare only the date portion (ignore time)
        $taskDueDate = new DateTime($task['due_date']);
        $taskDueDateString = $taskDueDate->format('Y-m-d'); // Due date in Y-m-d format
        
        // Compare current date and task due date
        if ($taskDueDateString === date('Y-m-d', strtotime('+1 day'))) {
            $notifContent = "Task '{$task['title']}' is due tomorrow.";
        } elseif ($taskDueDateString > date('Y-m-d', strtotime('+1 day'))) {
            $notifContent = "Task '{$task['title']}' is due on {$taskDueDate->format('Y-m-d')}.";
        } else {
            continue; // If the task is past due, no notification needed
        }

        // Check for duplicate notifications based on event_id and notif_content
        $checkNotifStmt = $pdo->prepare("
            SELECT COUNT(*) FROM notifications 
            WHERE event_id = :event_id AND notif_content = :notif_content
        ");
        $checkNotifStmt->execute([
            'event_id' => $task['id'],
            'notif_content' => $notifContent
        ]);
        $existingNotifCount = $checkNotifStmt->fetchColumn();

        if ($existingNotifCount == 0) {
            // Insert the notification if it doesn't already exist
            $notifStmt = $pdo->prepare("
                INSERT INTO notifications (user_id, type, notif_content, created_at, status, event_id) 
                VALUES (:user_id, :type, :notif_content, NOW(), 'unread', :event_id)
            ");
            $notifStmt->execute([
                'user_id' => $task['assignedTo'], // Notify the assigned user
                'type' => 'due_task', // Notification type
                'notif_content' => $notifContent,
                'event_id' => $task['id'] // Save the task_id under event_id
            ]);
        }
    }
} catch (PDOException $e) {
    // Log error to file
    log_error('Error fetching tasks or inserting notifications: ' . $e->getMessage(), 'notification_errors.txt');
    exit;
}

?>
