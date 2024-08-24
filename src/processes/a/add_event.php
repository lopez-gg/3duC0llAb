<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

$events = isset($_POST['events']) ? $_POST['events'] : [];

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, end_date, event_type ) VALUES (?, ?, ?, ?, ?)");

    foreach ($events as $event) {
        $title = isset($event['title']) ? $event['title'] : null;
        $description = isset($event['description']) ? $event['description'] : null;
        $start = isset($event['start']) ? $event['start'] : null;
        $end = isset($event['end']) ? $event['end'] : null;
        $type = isset($event['type']) ? $event['type'] : null;

        // Validate required fields
        if (!$title || !$description || !$start || !$type) {
            throw new Exception("Missing data for event: " . json_encode($event));
        }

        // Log event data for debugging
        // Uncomment the following line to see what is being processed
        error_log("Processing event: " . json_encode($event));

        if (!$stmt->execute([$title, $description, $start, $end, $type])) {
            throw new Exception("Failed to execute statement for event: " . json_encode($event));
        }
    }

    // Commit the transaction
    $pdo->commit();

    // Set success message and redirect
    $_SESSION['success_message'] = "Events added successfully!";
    // header("Location: ../../../public/admin/handle_events.php");
    echo 'event added';
    exit();
} catch (Exception $e) {
    // Rollback the transaction on error
    $pdo->rollBack();
    // Log the error
    log_error('Error adding events: ' . $e->getMessage(), 'db_errors.txt');

    // Set error message and redirect
    $_SESSION['error_message'] = "Failed to add events. Please try again later.";
    // header("Location: ../../../public/admin/handle_events.php");
    echo 'failed adding event lol';
    exit();
}

?>