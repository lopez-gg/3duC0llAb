<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

// Fetch the event data from the POST request
$event_id = isset($_POST['id']) ? $_POST['id'] : null;
$sy = isset($_POST['year_range']) ? $_POST['year_range'] : null;
$title = isset($_POST['title']) ? $_POST['title'] : null;
$description = isset($_POST['description']) ? $_POST['description'] : null;
$start = isset($_POST['start']) ? $_POST['start'] : null;
$end = isset($_POST['end']) ? $_POST['end'] : null;
$type = isset($_POST['type']) ? $_POST['type'] : null;

try {
    // Validate required fields
    if (!$event_id || !$sy || !$title || !$description || !$start || !$end || !$type) {
        throw new Exception("Missing data for event: " . json_encode($_POST));
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Prepare the SQL update statement
    $stmt = $pdo->prepare("
        UPDATE events
        SET title = ?, description = ?, event_date = ?, end_date = ?, event_type = ?, year_range = ?
        WHERE id = ?
    ");

    // Execute the statement with the correct parameters
    if (!$stmt->execute([$title, $description, $start, $end, $type, $sy, $event_id])) {
        throw new Exception("Failed to execute statement for event: " . json_encode($_POST));
    }

    // Commit the transaction
    $pdo->commit();

    // Set success message and redirect
    $_SESSION['success_title'] = "Success";
    $_SESSION['success_message'] = "Event updated successfully!";
    header("Location: ../../../public/admin/manage_events.php");
    exit();

} catch (Exception $e) {
    // Roll back transaction if active
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    log_error('Error updating event: ' . $e->getMessage(), 'db_errors.txt');
    $_SESSION['success_title'] = "Failed";
    $_SESSION['succes_message'] = "Failed to update event. Please try again later.";
    header("Location: ../../../public/admin/manage_events.php");
    exit();
}
?>
