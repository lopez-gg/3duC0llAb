<?php
// mark_notifications_read.php

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session_config.php';

header('Content-Type: application/json'); // Ensure the content type is JSON

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Get the user_id from the session

if ($user_id) {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET read_at = NOW() WHERE user_id = :user_id AND read_at IS NULL");
        $stmt->execute(['user_id' => $user_id]);
    } catch (PDOException $e) {
        log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    }
}
?>
