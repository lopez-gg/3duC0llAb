<?php
// mark_notifications_read.php

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session_config.php';

$data = json_decode(file_get_contents('php://input'), true);
$notification_ids = isset($data['ids']) ? $data['ids'] : [];

try {
    $placeholders = rtrim(str_repeat('?,', count($notification_ids)), ',');
    // Update status to 'read' and set read_at to current timestamp
    $stmt = $pdo->prepare("UPDATE notifications SET status = 'read', read_at = NOW() WHERE id IN ($placeholders) AND status = 'unread'");
    $stmt->execute($notification_ids);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['success' => false]);
}
?>
