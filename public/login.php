<?php

require_once __DIR__ . '/../src/config/session_config.php'; 
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
    <!-- <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self'; style-src 'self';"> -->
    <title>Login</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <h1>EduCollab</h1>
    <h2>Polangui South Central School</h2>
    <h3>112011</h3>
    

    <!-- Login form goes here -->
    <h1>Login</h1>
    <?php
    if (isset($_SESSION['login_error'])) {
        echo '<p class="error">' . $_SESSION['login_error'] . '</p>';
        unset($_SESSION['login_error']); // Clear the error message after displaying it
    }
    ?>
    <form action="../src/processes/login_process.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <br>
        <input type="submit" value="Login">
    </form>

    <footer>EduCollab 2024</footer>
</body>
</html>
