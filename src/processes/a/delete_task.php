<?php
require_once __DIR__ . '/../../config/db_config.php';

// Assuming you receive the task ID to delete via POST
$taskId = $_POST['id'] ?? null;
$grade = $_POST['grade'] ?? null;

if ($taskId) {
    try {
        $pdo->beginTransaction();

        // Move the task to archived_tasks
        $stmt = $pdo->prepare("
            INSERT INTO archived_tasks
            (id, assignedBy, assignedTo, title, description, taskType, tag, grade, progress, status, created_at, due_date, due_time, completed_at, deleted_at)
            SELECT id, assignedBy, assignedTo, title, description, taskType, tag, grade, progress, 'archived', created_at, due_date, due_time, completed_at, NOW()
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
        // header('Location: /admin/space_home.php?grade=' . $grade);
        echo 'positive' . $grade;
            
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        // Handle the error
        error_log('Error moving task to archived_tasks: ' . $e->getMessage());
        echo 'negative' . $grade;
        log_error('Error fetching tasks: ' . $e->getMessage(), 'db_errors.txt');
        // Optionally show a user-friendly error message
        $_SESSION['error_message'] = 'An error occurred while deleting the task.';
        // header('Location: /admin/space_home.php?grade=' . $grade);
    }
}
?>
