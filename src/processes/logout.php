<?php

require_once __DIR__ . '/../config/session_config.php';
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