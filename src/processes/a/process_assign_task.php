<?php
require_once __DIR__ . '/../../config/session_config.php'; // Include session config
require_once __DIR__ . '/../../config/db_config.php'; // Include database config

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $assignedTo = (int)$_POST['assignedTo']; // Get assignedTo from hidden input
    $due_date = $_POST['due_date'] ? $_POST['due_date'] : null;

    // Automatically set taskType and assignedBy from the session
    $taskType = 'assigned';
    $assignedBy = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("
            INSERT INTO tasks (assignedBy, assignedTo, title, description, taskType, status, created_at, due_date)
            VALUES (:assignedBy, :assignedTo, :title, :description, :taskType, 'pending', NOW(), :due_date)
        ");
        $stmt->bindParam(':assignedBy', $assignedBy);
        $stmt->bindParam(':assignedTo', $assignedTo);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':taskType', $taskType);
        $stmt->bindParam(':due_date', $due_date);

        if ($stmt->execute()) {
            $_SESSION['success_title'] = "Success";
            $_SESSION['success_message'] = "Task posted successfully!";
            header("Location: ../../../public/admin/tasks.php");
            exit;
        } 
    } catch (Exception $e) {
        $_SESSION['success_title'] = "Failed";
        $_SESSION['success_message'] = "Failed to post task, please try again later.";
        log_error('Task creation failed: ' . $e->getMessage(), 'error.log');
        header("Location: ../../../public/admin/assign_task.php");
        exit;
    }
} else {
    header("Location: ../../../public/admin/assign_task.php");
    exit;
}
?>
