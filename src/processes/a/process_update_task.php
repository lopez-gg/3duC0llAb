<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

// Validate the CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

// Fetch task data from POST request
$task_id = isset($_POST['id']) ? $_POST['id'] : null;
$title = isset($_POST['title']) ? $_POST['title'] : null;
$description = isset($_POST['description']) ? $_POST['description'] : null;
$assignedTo = isset($_POST['assignedTo']) ? $_POST['assignedTo'] : null;
$tag = isset($_POST['tag']) ? $_POST['tag'] : null;
$progress = isset($_POST['progress']) ? $_POST['progress'] : null;
$due_date = isset($_POST['due_date']) ? $_POST['due_date'] : null;
$due_time = isset($_POST['due_time']) ? $_POST['due_time'] : null;

if (empty($task_id) || empty($title) || empty($assignedTo) || empty($progress)) {
    die("Missing required data");
}

try {
    // Fetch the existing grade from the database
    $gradeQuery = $pdo->prepare("SELECT grade FROM tasks WHERE id = ?");
    $gradeQuery->execute([$task_id]);
    $task = $gradeQuery->fetch();

    if (!$task) {
        die("Task not found");
    }

    $grade = $task['grade'];  // Keep the existing grade

    // Begin transaction
    $pdo->beginTransaction();

    // Prepare SQL update statement
    $stmt = $pdo->prepare("
        UPDATE tasks
        SET title = ?, description = ?, tag = ?, grade = ?, progress = ?, due_date = ?, due_time = ?, updated_at = NOW()
        WHERE id = ?
    ");

    // Execute the statement with the provided values
    $stmt->execute([$title, $description, $tag, $grade, $progress, $due_date, $due_time, $task_id]);

    // Commit the transaction
    $pdo->commit();

    // Set success message and redirect
    $_SESSION['success_title'] = "Success";
    echo $_SESSION['success_message'] = "Task updated successfully!";
    exit();

} catch (Exception $e) {
    // Rollback transaction if active
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Log the error and show a failure message
    log_error('Error updating task: ' . $e->getMessage(), 'db_errors.txt');
    $_SESSION['success_title'] = "Failed";
    echo $_SESSION['success_message'] = "Failed to update task. Please try again later.";
    exit();
}
