<?php
require_once __DIR__ . '/../../config/db_config.php'; // Uses the existing PDO connection
require_once __DIR__ . '/../../config/config.php';

// Get the grade from the request
$grade = isset($_GET['grade']) ? $_GET['grade'] : null;

if ($grade) {
    try {
        // Prepare the SQL query to fetch tasks with user information based on the grade
        $stmt = $pdo->prepare("
            SELECT t.id, t.title, t.description, t.taskType, t.tag, t.grade, t.status, t.created_at, t.due_date, 
                   u.username AS assigned_username
            FROM tasks t
            LEFT JOIN users u ON t.assignedTo = u.id
            WHERE t.grade = :grade
            ORDER BY t.created_at DESC
        ");
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch all tasks
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the tasks as JSON
        echo json_encode($tasks);

    } catch (PDOException $e) {
        // Log the error and return a JSON error message
        log_error('Error fetching tasks: ' . $e->getMessage(), 'db_errors.txt');
        echo json_encode(['error' => 'Failed to fetch tasks']);
    }
} else {
    echo json_encode(['error' => 'Grade not specified']);
}
?>
