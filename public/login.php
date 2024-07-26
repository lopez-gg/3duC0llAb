<?php
// login.php

// Include necessary configurations
require_once __DIR__ . '/../src/config/session_config.php'; // Start session management
require_once __DIR__ . '/../src/config/db_config.php'; 

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Query the database to get user details
    $stmt = $pdo->prepare("SELECT accType FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Redirect based on user role
        if ($user['accType'] === 'ADMIN') {
            header('Location: admin/dashboard.php');
        } elseif ($user['accType'] === 'USER') {
            header('Location: user/dashboard.php');
        } else {
            // Handle unexpected role
            header('Location: public/login.php'); // Redirect to login if role is unexpected
        }
        exit;
    } else {
        // Handle user not found
        header('Location: public/login.php'); // Redirect to login if user not found
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <!-- Login form goes here -->
    <h1>Login</h1>
    <form action="../src/processes/login_process.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
