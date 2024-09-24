<?php
// faculty_process.php
require_once __DIR__ . '/../config/db_config.php'; // Database configuration
require_once __DIR__ . '/../config/config.php'; // General config for logging

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'];

        if ($action === 'add') {
            // Add new faculty logic here
            $username = $_POST['username'];
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $gradeLevel = $_POST['gradeLevel'];
            $status = $_POST['status'];

            // Insert new faculty into the database
            $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, gradeLevel, status, accType) VALUES (?, ?, ?, ?, ?, 'FACULTY')");
            $stmt->execute([$username, $firstname, $lastname, $gradeLevel, $status]);

            // Redirect after successful insert
            header('Location: faculty.php');
            exit;
        }
        // Additional cases for updating or deleting faculty can go here

    } catch (PDOException $e) {
        // Log the error and show a user-friendly message
        log_error('Error inserting faculty: ' . $e->getMessage(), 'db_errors.txt');
        echo 'Error adding faculty member. Please try again.';
        exit;
    }
}
?>
