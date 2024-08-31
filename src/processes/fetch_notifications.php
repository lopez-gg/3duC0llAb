<?php
require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/config.php'; 
require_once __DIR__ . '/../config/session_config.php';

header('Content-Type: application/json'); // Ensure the content type is JSON

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Default values
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

try {
    // Prepare and execute the query to fetch both user-specific and general notifications
    $query = "
        SELECT * 
        FROM notifications 
        WHERE (user_id = :user_id OR user_id IS NULL) 
          AND read_at IS NULL
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $pdo->prepare($query);

    // Bind parameters
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    // Only bind user_id if it is not null
    if ($user_id) {
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    } else {
        // Bind null value for user_id to avoid missing parameter issues
        $stmt->bindValue(':user_id', null, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    
    // Fetch all relevant notifications
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($notifications)) {
        $notifications = ['message' => 'No recent notifications'];
    }

} catch (PDOException $e) {
    log_error('Database query failed: ' . $e->getMessage(), 'db_errors.txt');
    $notifications = ['message' => 'Error fetching notifications'];
}

// Return the notifications array as JSON
echo json_encode(['notifications' => $notifications]);
