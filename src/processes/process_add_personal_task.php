<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php'; 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = $_POST['title'];
        $description = !empty($_POST['description']) ? $_POST['description'] : null;
        $urgency = $_POST['urgency'];
        $due_date = $_POST['due_date'];
        $due_time = $_POST['due_time'];
        $assignedTo = $_SESSION['user_id'];  // Admin is assigning to themselves
        $assignedBy = $_SESSION['user_id'];
        $taskType = 'private';  // Task is for personal use

        // Prepare SQL query to insert the task
        $query = "
            INSERT INTO tasks (title, description, tag, due_date, due_time, assignedTo, assignedBy, taskType, grade, progress)
            VALUES (:title, :description, :urgency, :due_date, :due_time, :assignedTo, :assignedBy, :taskType, NULL, 'pending')
        ";

        $stmt = $pdo->prepare($query);

        // Bind parameters to the SQL query
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':urgency', $urgency);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':due_time', $due_time);
        $stmt->bindParam(':assignedTo', $assignedTo);
        $stmt->bindParam(':assignedBy', $assignedBy);
        $stmt->bindParam(':taskType', $taskType);

        // Execute the query
        $stmt->execute();

        // Set success message and redirect back to the My Space page
        $_SESSION['success_message'] = 'Personal task successfully created!';
        header('Location: ../../my_space.php');
        exit;
    } catch (PDOException $e) {
        // Log the error and return an error message
        log_error('Error creating task: ' . $e->getMessage(), 'db_errors.txt');
        $_SESSION['error_message'] = 'Failed to create task.';
        header('Location: ../../my_space.php');
        exit;
    }
} else {
    header('Location: ../../my_space.php');
    exit;
}
