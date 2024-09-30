<?php
// login_process.php

// Include necessary configurations
require_once __DIR__ . '/../config/config.php'; // Load general configurations
require_once __DIR__ . '/../config/db_config.php'; // Database configuration
require_once __DIR__ . '/../config/session_config.php'; // Session management

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve user input
    $username = htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8');

    if ($username && $password) {
        try {
            // Prepare and execute the query
            $stmt = $pdo->prepare("SELECT id, password, status, accType FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // if ($user) {
            //     echo 'User found: ';
            //     print_r($user);
            // } else {
            //     echo 'User not found.';
            // }

            // Track login attempt
            $loginSuccess = 0; // Default to failure
            if ($user && password_verify($password, $user['password'])) {
                $loginSuccess = 1; // Successful login
                
                // Check if the user status is 'inactive'
                if ($user['status'] === 'inactive') {
                    // Update status to 'active'
                    $updateStmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                }

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $username;
                $_SESSION['accType'] = $user['accType']; // Store accType in session

                // Insert login attempt into logins table
                $logStmt = $pdo->prepare("INSERT INTO logins (user_id, success) VALUES (?, ?)");
                $logStmt->execute([$user['id'], $loginSuccess]);

                // Redirect to the appropriate dashboard
                if ($user['accType'] === 'ADMIN') {
                    header('Location: ../../public/admin/dashboard.php');
                } elseif ($user['accType'] === 'USER') {
                    header('Location: ../../public/user/dashboard.php');
                } else {
                    echo 'Invalid account type.';
                }
                exit;
            } else {
                // Insert failed login attempt into logins table
                if ($user) {
                    $logStmt = $pdo->prepare("INSERT INTO logins (user_id, success) VALUES (?, ?)");
                    $logStmt->execute([$user['id'], $loginSuccess]);
                }

                // Invalid credentials
                $_SESSION['login_error'] = 'Invalid username or password.';
                header('Location: ../../public/login.php'); // Redirect back to the login page
                exit;
            }
        } catch (PDOException $e) {
            // Log error and display a user-friendly message
            log_error('Login failed: ' . $e->getMessage(), '../../logs/user_errors.txt');
            $_SESSION['login_error'] = 'An error occurred. Please try again later.';
            header('Location: ../../public/login.php'); // Redirect back to the login page
            exit;
        }
    } else {
        // Missing username or password
        $_SESSION['login_error'] = 'Please enter both username and password.';
        header('Location: ../../public/login.php'); // Redirect back to the login page
        exit;
    }
} else {
    // Redirect to login page if not a POST request
    header('Location: ../../public/login.php');
    exit;
}
?>
