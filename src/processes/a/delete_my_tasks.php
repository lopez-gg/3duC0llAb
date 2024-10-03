<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';

// Check for CSRF token in POST request
$csrfToken = $_POST['csrf_token'] ?? null;

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// Assuming you receive the task ID to delete via POST
$taskId = $_POST['id'] ?? null;

if ($taskId) {
    try {
        $pdo->beginTransaction();

        // Move the task to archived_tasks
        $stmt = $pdo->prepare("
            INSERT INTO archived_tasks
            (id, assignedBy, assignedTo, title, description, taskType, tag, progress, status, created_at, due_date, due_time, completed_at, deleted_at)
            SELECT id, assignedBy, assignedTo, title, description, taskType, tag, progress, 'archived', created_at, due_date, due_time, completed_at, NOW()
            FROM tasks
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the task from the tasks table
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->bindParam(':id', $taskId, PDO::PARAM_INT);
        $stmt->execute();

        $pdo->commit();

        // Redirect or respond with success
        $_SESSION['success_message'] = 'Task successfully deleted.';
        header('Location: ../../../public/admin/my_space.php');
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        // Handle the error
        log_error('Error deleting task: ' . $e->getMessage(), 'db_errors.txt');
        $_SESSION['error_message'] = 'An error occurred while deleting the task.';
    }
}
?>
