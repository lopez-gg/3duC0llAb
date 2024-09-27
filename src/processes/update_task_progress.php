<?php
require_once __DIR__ . '/../config/config.php'; // Load general configurations
require_once __DIR__ . '/../config/db_config.php'; // Database configuration
require_once __DIR__ . '/../config/session_config.php'; // Session management

// Fetch task data from POST request
$task_id = isset($_POST['id']) ? $_POST['id'] : null;
$progress = isset($_POST['progress']) ? $_POST['progress'] : null;

if (empty($task_id) || empty($progress)) {
    die("Missing required data");
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Prepare SQL update statement for progress only
    $stmt = $pdo->prepare("
        UPDATE tasks
        SET progress = ?, updated_at = NOW()
        WHERE id = ?
    ");

    // Execute the statement with the provided values
    $stmt->execute([$progress, $task_id]);

    // Commit the transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Task progress updated successfully!'  
    ]);


} catch (Exception $e) {
    // Rollback transaction if active
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Log the error and show a failure message
    log_error('Error updating task progress: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['success' => false, 'message' => 'Failed to update task progress.']);
    exit();
}
