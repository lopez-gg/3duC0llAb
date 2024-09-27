<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

// Validate the CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch task data from POST request
$task_id = isset($_POST['id']) ? $_POST['id'] : null;
$title = isset($_POST['title']) ? $_POST['title'] : null;
$description = isset($_POST['description']) ? $_POST['description'] : null;
$tag = isset($_POST['tag']) ? $_POST['tag'] : null;
$progress = isset($_POST['progress']) ? $_POST['progress'] : null;
$due_date = isset($_POST['due_date']) ? $_POST['due_date'] : null;
$due_time = isset($_POST['due_time']) ? $_POST['due_time'] : null;

if (empty($task_id) || empty($title) || empty($progress)) {
    die("Missing required data");
}

try {
    // Fetch the task from the database and ensure it belongs to the current user
    $taskQuery = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND assignedBy = ? AND taskType = 'private'");
    $taskQuery->execute([$task_id, $user_id]);
    $task = $taskQuery->fetch();

    if (!$task) {
        die("Task not found or you do not have permission to update this task");
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Prepare SQL update statement
    $stmt = $pdo->prepare("
        UPDATE tasks
        SET title = ?, description = ?, tag = ?, progress = ?, due_date = ?, due_time = ?, updated_at = NOW()
        WHERE id = ? AND assignedBy = ? AND assignedTo = ? AND taskType = 'private'
    ");

    // Execute the statement with the provided values
    $stmt->execute([$title, $description, $tag, $progress, $due_date, $due_time, $task_id, $user_id, $user_id]);

    // Commit the transaction
    $pdo->commit();

    // Set success message and redirect
    $_SESSION['success_title'] = "Success";
    $_SESSION['success_message'] = "Task updated successfully!";
    header('Location: ../../../public/admin/my_space.php');
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
    header('Location: ../../../public/admin/my_space.php');
    exit();
}
