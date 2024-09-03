<?php
// mark_notifications_read.php

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session_config.php';

$data = json_decode(file_get_contents('php://input'), true);
$notification_id = isset($data['id']) ? $data['id'] : null;

try {
    $stmt = $pdo->prepare("UPDATE notifications SET status = 'read' WHERE id = :id AND status = 'unread'");
    $stmt->execute(['id' => $notification_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['success' => false]);
}
?>
