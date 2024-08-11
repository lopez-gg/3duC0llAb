<?php
// access_control.php
require_once __DIR__ . '/../config/session_config.php'; // Include session configuration


function check_access($requiredRole) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /educollab/public/login.php'); // Redirect to login page if not logged in
        exit;
    }

    if ($_SESSION['accType'] !== $requiredRole) {
        header('Location: /educollab/public/access_denied.php'); // Redirect to access denied page if not authorized
        exit;
    }
}
