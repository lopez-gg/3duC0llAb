<?php
// login_process.php

// Include necessary configurations
require_once __DIR__ . '/config.php'; // Load general configurations
require_once __DIR__ . '/db_config.php'; // Database configuration
require_once __DIR__ . '/session_config.php'; // Session management

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            // Prepare and execute the query
            $stmt = $pdo->prepare("SELECT id, password, status FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Check if the user status is 'inactive'
                if ($user['status'] === 'inactive') {
                    // Update status to 'active'
                    $updateStmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                }

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;

                // Redirect to the dashboard
                header('Location: dashboard.php');
                exit;
            } else {
                // Invalid credentials
                echo 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            // Log error and display a user-friendly message
            log_error('Login failed: ' . $e->getMessage(), 'user_errors.txt');
            echo 'An error occurred. Please try again later.';
        }
    } else {
        // Missing username or password
        echo 'Please enter both username and password.';
    }
} else {
    // Redirect to login page if not a POST request
    header('Location: login.php');
    exit;
}
?>
