<!-- /controllers/process_add_task.php -->
<?php
require_once __DIR__ . '/../config/session_config.php'; // Include session config
require_once __DIR__ . '/../config/db_config.php'; // Include database config

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form inputs
    $title = trim($_POST['title']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $assignedTo = (int)$_POST['assignedTo'];
    $taskType = $_POST['taskType'];
    $tag = $_POST['tag'];
    $due_date = $_POST['due_date'] ? $_POST['due_date'] : null;

    // Assuming you have session user data stored in $_SESSION['user_id']
    $assignedBy = $_SESSION['user_id']; // Adjust according to your session data

    try {
        // Prepare SQL query to insert the new task
        $stmt = $pdo->prepare("
            INSERT INTO tasks (assignedBy, assignedTo, title, description, taskType, tag, status, created_at, due_date)
            VALUES (:assignedBy, :assignedTo, :title, :description, :taskType, :tag, 'pending', NOW(), :due_date)
        ");
        $stmt->bindParam(':assignedBy', $assignedBy);
        $stmt->bindParam(':assignedTo', $assignedTo);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':taskType', $taskType);
        $stmt->bindParam(':tag', $tag);
        $stmt->bindParam(':due_date', $due_date);

        // Execute the query
        if ($stmt->execute()) {
            // Redirect to a success page or back to the tasks list
            header("Location: ../views/tasks.php?success=1");
        } else {
            throw new Exception("Failed to add task");
        }
    } catch (Exception $e) {
        // Log the error and redirect to an error page or show an error message
        log_error('Task insertion failed: ' . $e->getMessage(), 'task_errors.txt');
        header("Location: ../views/add_new_task.php?error=1");
        exit;
    }
} else {
    // If accessed directly, redirect to form
    header("Location: ../views/add_new_task.php");
    exit;
}
?>
