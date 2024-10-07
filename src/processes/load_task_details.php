<?php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
}

$taskId = $_GET['id'];  
// $taskId = 8;

header('Content-Type: application/json');
try {
    $stmt = $pdo->prepare("
        SELECT t.id, t.title, t.description, t.taskType, t.tag, t.grade, t.progress, t.created_at, t.due_date, t.due_time, 
               u_assigned.username AS assigned_username,
               u_by.username AS assigned_by_username
        FROM tasks t
        LEFT JOIN users u_assigned ON t.assignedTo = u_assigned.id
        LEFT JOIN users u_by ON t.assignedBy = u_by.id
        WHERE t.id = :task_id
    ");
    $stmt->execute(['task_id' => $taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        echo json_encode(['error' => 'No task found for the given ID']);
    } else {
        echo json_encode($task);
    }
} catch (PDOException $e) {
    log_error('Error loading event details: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['error' => 'Database error occurred']);
}

