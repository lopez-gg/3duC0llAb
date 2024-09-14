<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

// Fetch task data from POST request
$task_id = isset($_POST['id']) ? $_POST['id'] : null;
$title = isset($_POST['title']) ? $_POST['title'] : null;
$description = isset($_POST['description']) ? $_POST['description'] : null;
$taskType = isset($_POST['taskType']) ? $_POST['taskType'] : null;
$tag = isset($_POST['tag']) ? $_POST['tag'] : null;
$grade = isset($_POST['grade']) ? $_POST['grade'] : null;
$progress = isset($_POST['progress']) ? $_POST['progress'] : null;
$due_date = isset($_POST['due_date']) ? $_POST['due_date'] : null;
$due_time = isset($_POST['due_time']) ? $_POST['due_time'] : null;

try {
    // Validate required fields
    if (!$task_id || !$title || !$description || !$taskType || !$progress) {
        throw new Exception("Missing data for task: " . json_encode($_POST));
    }

    // Begin transaction
    $pdo->beginTransaction();

    // Prepare SQL update statement including updated_at column
    $stmt = $pdo->prepare("
        UPDATE tasks
        SET title = ?, description = ?, tag = ?, grade = ?, progress = ?, due_date = ?, due_time = ?, updated_at = NOW()
        WHERE id = ?
    ");

    // Execute the statement with the provided values
    if (!$stmt->execute([$title, $description, $taskType, $tag, $grade, $progress, $due_date, $due_time, $task_id])) {
        throw new Exception("Failed to execute statement for task: " . json_encode($_POST));
    }

    // Commit the transaction
    $pdo->commit();

    // Set success message and redirect
    $_SESSION['success_title'] = "Success";
  echo  $_SESSION['success_message'] = "Task updated successfully!";
    // header("Location: ../../../public/admin/manage_tasks.php");
    exit();

} catch (Exception $e) {
    // Rollback transaction if active
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    log_error('Error updating task: ' . $e->getMessage(), 'db_errors.txt');
    $_SESSION['success_title'] = "Failed";
   echo  $_SESSION['success_message'] = "Failed to update task. Please try again later.";
    // header("Location: ../../../public/admin/manage_tasks.php");
    exit();
}
