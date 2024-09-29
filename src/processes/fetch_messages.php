<?php

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../config/session_config.php';

$userId = $_SESSION['user_id']; // Assume user ID is stored in session
$limit = 10; // Number of messages per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit;


// Fetch messages
$sql = "SELECT m.*, u1.username AS sender_username, u2.username AS recipient_username 
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.id 
        JOIN users u2 ON m.recipient_id = u2.id 
        WHERE m.recipient_id = ? OR m.sender_id = ? 
        ORDER BY m.created_at DESC
        LIMIT ?, ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("iiii", $userId, $userId, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Count total messages for pagination
$countSql = "SELECT COUNT(*) as total FROM messages WHERE recipient_id = ? OR sender_id = ?";
$countStmt = $connection->prepare($countSql);
$countStmt->bind_param("ii", $userId, $userId);
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalCount = $countResult->fetch_assoc()['total'];

$response = [
    'messages' => $messages,
    'total' => $totalCount,
    'limit' => $limit,
    'page' => $page,
];

echo json_encode($response);
?>
