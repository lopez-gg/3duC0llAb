<?php
// session_config.php

require_once __DIR__ . '/config.php'; // Includes general configuration

// Start the session
session_start();

// Regenerate session ID to prevent session fixation attacks
if (session_id() == '') {
    session_regenerate_id(true);
}

// Optional: Set session cookie parameters for enhanced security
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Optional: Regenerate session ID periodically for added security
if (!isset($_SESSION['last_regeneration']) || time() - $_SESSION['last_regeneration'] > 3600) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}
?>
