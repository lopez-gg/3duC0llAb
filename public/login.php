<?php
// login.php

// Include necessary configurations
require_once __DIR__ . '/../src/config/config.php'; // Load general configurations
require_once __DIR__ . '/../src/session_config.php'; // Start session management


// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: user/dashboard.php'); // Redirect to dashboard if already logged in
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS -->
</head>
<body>
    <form action="login_process.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
</body>
</html>
