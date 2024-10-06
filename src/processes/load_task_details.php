<?php
//deets for reminder
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
}

$taskId = $_GET['task_id']; 

try {
    $stmt = $pdo->prepare("
        SELECT id, title, description, taskType, due_date, due_time, progress
        FROM tasks
        WHERE id = :task_id
    ");

    $stmt->execute(['task_id' => $taskId]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($task);

} catch (PDOException $e) {
    log_error('Error loading task details: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode([]);
}
?>
