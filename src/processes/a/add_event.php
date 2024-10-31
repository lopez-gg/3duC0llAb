<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

$events = isset($_POST['events']) ? $_POST['events'] : [];
$userID = $_SESSION['user_id'];

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, end_date, event_type, year_range) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($events as $event) {
        $sy = isset($event['year_range']) ? $event['year_range'] : null;
        $title = isset($event['title']) ? $event['title'] : null;
        $description = isset($event['description']) ? $event['description'] : null;
        $start = isset($event['start']) ? $event['start'] : null;
        $end = isset($event['end']) ? $event['end'] : null;
        $type = isset($event['type']) ? $event['type'] : null;

        // Validate required fields
        if (!$sy || !$title || !$description || !$start || !$end || !$type) {
            throw new Exception("Missing data for event: " . json_encode($event));
        }

        if (!$stmt->execute([$title, $description, $start, $end, $type, $sy])) {
            throw new Exception("Failed to execute statement for event: " . json_encode($event));
        }
    }

    $pdo->commit();

    
    // if($pdo->commit()){
    //     $events_sql = 'SELECT id FROM events WHERE added_at = NOW(YYYY-MMMM-DD) AND year_range =' . $sy;
    //     $events_stmt = $pdo->prepare($events_sql);
    //     $events_stmt->execute();
    //     $events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);

    //     return [
    //         'subject_id' => $events
    //     ];

    //     $activity_message = '[ADDED] New event/s for SY ' . $sy;

    //     add_activity_history($userID, $subject_id[], $activity_message);
    // }
    

    $_SESSION['success_message'] = "Events added successfully!";
    header("Location: ../../../public/admin/manage_events.php?");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    log_error('Error adding events: ' . $e->getMessage(), 'db_errors.txt');
    $_SESSION['error_message'] = "Failed to add events. Please try again later.";
    header("Location: ../../../public/admin/manage_events.php");
    exit();
}
?>
