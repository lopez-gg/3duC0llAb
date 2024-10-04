<?php
require_once __DIR__ . '/../../config/session_config.php'; // Include session config
require_once __DIR__ . '/../../config/db_config.php'; // Include database config

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = isset($_POST['description']) ? trim($_POST['description']) : null;
    $assignedTo = isset($_POST['assignedTo']) ? (int)$_POST['assignedTo'] : null; 
    $grade = isset($_POST['grade']) ? $_POST['grade'] : null; 
    $due_date = isset($_POST['due_date']) ? $_POST['due_date'] : null;
    $urgency = isset($_POST['urgency']) ? $_POST['urgency'] : 'Normal'; 
    $due_time = isset($_POST['due_time']) ? $_POST['due_time'] : null; 

    // Automatically set taskType and assignedBy from the session
    $taskType = 'assigned';
    $assignedBy = $_SESSION['user_id'];

    try {
        // Insert task into the tasks table
        $stmt = $pdo->prepare("
            INSERT INTO tasks (assignedBy, assignedTo, grade, title, description, taskType, tag, progress, created_at, due_date, due_time)
            VALUES (:assignedBy, :assignedTo, :grade, :title, :description, :taskType, :urgency, 'pending', NOW(), :due_date, :due_time)
        ");
        $stmt->bindParam(':assignedBy', $assignedBy);
        $stmt->bindParam(':assignedTo', $assignedTo);
        $stmt->bindParam(':grade', $grade);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':taskType', $taskType);
        $stmt->bindParam(':urgency', $urgency); 
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':due_time', $due_time);

        if ($stmt->execute()) {
            // Retrieve the last inserted task ID
            $taskId = $pdo->lastInsertId();

            // Insert notification into the notifications table
            $notifContent = "You have been assigned a new task: " . htmlspecialchars($title);
            $notifType = 'info'; // You can change this based on your notification types
            
            $notifStmt = $pdo->prepare("
                INSERT INTO notifications (user_id, type, notif_content, created_at, status, event_id)
                VALUES (:user_id, :type, :notif_content, NOW(), 'unread', :event_id)
            ");
            $notifStmt->bindParam(':event_id', $taskId); // Use the retrieved task ID
            $notifStmt->bindParam(':user_id', $assignedTo);
            $notifStmt->bindParam(':type', $notifType);
            $notifStmt->bindParam(':notif_content', $notifContent);
            $notifStmt->execute();

            // Set success message and redirect
            $_SESSION['success_title'] = "Success";
            $_SESSION['success_message'] = "Task posted successfully!";
            header("Location: ../../../public/admin/space_home.php?grade=" . urlencode($grade));
            exit;
        } 
    } catch (Exception $e) {
        $_SESSION['success_title'] = "Failed";
        $_SESSION['success_message'] = "Failed to post task, please try again later.";
        log_error('Task creation failed: ' . $e->getMessage(), 'error.log');
        header("Location: ../../../public/admin/space_home.php?grade=" . urlencode($grade));
        exit;
    }
} else {
    header("Location: ../../../public/space_home.php?grade=" . urlencode($grade));
    exit;
}
?>
