<?php
require_once __DIR__ . '/../../config/db_config.php'; // Uses the existing PDO connection
require_once __DIR__ . '/../../config/config.php';

// Get the grade from the request
$grade = isset($_GET['grade']) ? $_GET['grade'] : null;

if ($grade) {
    try {
        // Prepare the SQL query to fetch tasks based on the grade
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE grade = :grade ORDER BY created_at DESC");
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
