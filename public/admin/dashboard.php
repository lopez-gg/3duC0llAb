<?php
// dashboard.php

require_once __DIR__ . '/../../src/config/access_control.php'; // Include access control script
require_once __DIR__ . '/../../src/config/session_config.php';

// Check if the user is an ADMIN
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /../login.php'); // Redirect to login page if not logged in
    exit;
}
// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Destroy the session
    session_unset();
    session_destroy();
    
    // Redirect to the login page
    header('Location: ../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! This is your dashboard.</h1>

    <!-- Logout Form -->
    <form action="" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

    <a href="add_user.php">add new user<a>
</body>
</html>
