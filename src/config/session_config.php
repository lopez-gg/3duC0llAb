<?php
// session_config.php

require_once __DIR__ . '/../config/config.php'; // Includes general configuration

// Optional: Set session cookie parameters for enhanced security
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Start the session if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) { 
    ini_set('session.cookie_path', '/');
    session_start();
}

// Regenerate session ID to prevent session fixation attacks
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Optional: Regenerate session ID periodically for added security
if (isset($_SESSION['last_regeneration']) && time() - $_SESSION['last_regeneration'] > 3600) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Generate a CSRF token if one doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
}


?>
