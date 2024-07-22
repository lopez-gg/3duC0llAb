<?php
// dashboard.php

// Include necessary configurations
require_once __DIR__ . '/config.php'; // Load general configurations
require_once __DIR__ . '/session_config.php'; // Start session management

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

echo 'Welcome, ' . htmlspecialchars($_SESSION['username']) . '!' . 'This is your dashboard.';
?>
