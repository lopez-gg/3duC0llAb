<?php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/session_config.php';

header('Content-Type: application/json');

// Assuming the user's ID is stored in the session
$userId = $_SESSION['user_id'];

$currentDate = date('Y-m-d'); 

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
try {
    // Fetch reminders for today
    $stmt = $pdo->prepare("
        SELECT r.id, r.reminder_type, r.reminder_date, r.reminder_message, r.status, r.task_id
        FROM reminders r
        WHERE r.user_id = :user_id 
        AND r.status = 'waiting'
        AND r.reminder_date = :currentDate;
    ");
    // Execute the query with both parameters
    $stmt->execute(['user_id' => $userId, 'currentDate' => $currentDate]);
    
    $reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch titles from either the tasks or events table based on reminder_type
    if (empty($reminders)){
        echo json_encode(['reminders' => [], 'message' => 'You have no reminders set for today.']);
        exit;
    }else {

    foreach ($reminders as &$reminder) {
        if ($reminder['reminder_type'] === 'task') {
            // Fetch task title
            $taskStmt = $pdo->prepare("SELECT title FROM tasks WHERE id = :task_id");
            $taskStmt->execute(['task_id' => $reminder['task_id']]);
            $taskTitle = $taskStmt->fetchColumn();
            $reminder['title'] = $taskTitle ?: 'Untitled Task';  // Default if no title
        } else if ($reminder['reminder_type'] === 'event') {
            // Fetch event title
            $eventStmt = $pdo->prepare("SELECT title FROM events WHERE id = :event_id");
            $eventStmt->execute(['event_id' => $reminder['task_id']]);
            $eventTitle = $eventStmt->fetchColumn();
            $reminder['title'] = $eventTitle ?: 'Untitled Event';  // Default if no title
        }

        // Provide a default message if reminder_message is NULL
        $reminder['reminder_message'] = $reminder['reminder_message'] ?: 'No additional message provided';
    }
}

    // Return the reminders with titles in JSON format
    echo json_encode($reminders);

} catch (PDOException $e) {
    log_error('Error fetching reminders: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode([]);
}
?>
