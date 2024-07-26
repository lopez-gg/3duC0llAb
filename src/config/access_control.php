<?php
// access_control.php

require_once __DIR__ . '/session_config.php'; // Include session configuration

// Check if the user is logged in and has the appropriate role
function check_access($requiredRole) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../public/login.php'); // Redirect to login page if not logged in
        exit;
    }

    if ($_SESSION['accType'] !== $requiredRole) {
        header('Location: access_denied.php'); // Redirect to access denied page if not authorized
        exit;
    }
}
?>
