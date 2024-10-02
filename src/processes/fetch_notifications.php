<?php
// fetch_notifications.php

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../config/session_config.php';

header('Content-Type: application/json'); // Ensure the content type is JSON

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null; // Get the user_id from the session

function timeAgo($timestamp) {
    $timestamp = strtotime($timestamp);
    $current_time = time(); // Get the current time
    $time_difference = $current_time - $timestamp; // Time difference in seconds

    if ($time_difference < 0) {
        return 'In the future'; 
    } elseif ($time_difference < 60) {
        return $time_difference . ' seconds ago';
    } elseif ($time_difference < 3600) {
        return floor($time_difference / 60) . ' minutes ago';
    } elseif ($time_difference < 86400) {
        return floor($time_difference / 3600) . ' hours ago';
    } elseif ($time_difference < 604800) {
        return floor($time_difference / 86400) . ' days ago';
    } elseif ($time_difference < 2419200) {
        return floor($time_difference / 604800) . ' weeks ago';
    } elseif ($time_difference < 29030400) {
        return floor($time_difference / 2419200) . ' months ago';
    } else {
        return floor($time_difference / 29030400) . ' years ago';
    }
}

try {
    // Query to fetch the notifications
    $query = "
        SELECT id, notif_content, created_at,
            CASE 
                WHEN type = 'calendar_event' AND DATE(created_at) < CURDATE() THEN 'past'
                WHEN read_at IS NULL THEN 'unread'
                ELSE 'read'
            END AS status
        FROM notifications 
        WHERE (user_id = :user_id OR user_id IS NULL)
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($query);
    $limit = $_GET['limit'];
    $offset = ($_GET['page'] - 1) * $limit;

    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add "time ago" calculation to each notification
    foreach ($notifications as &$notification) {
        $notification['time_ago'] = timeAgo($notification['created_at']);
    }

    if (empty($notifications)) {
        $notifications = ['message' => 'No recent notifications'];
    }

    // Query to count unread notifications
    $unreadCountQuery = "
        SELECT COUNT(*) AS unread_count
        FROM notifications
        WHERE (user_id = :user_id OR user_id IS NULL)
        AND read_at IS NULL
    ";

    $unreadStmt = $pdo->prepare($unreadCountQuery);
    $unreadStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $unreadStmt->execute();

    $unreadCount = $unreadStmt->fetch(PDO::FETCH_ASSOC)['unread_count'];

} catch (PDOException $e) {
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    $notifications = ['message' => 'Error fetching notifications'];
    $unreadCount = 0;
}

echo json_encode([
    'notifications' => $notifications,
    'unread_count' => $unreadCount 
]);
?>
