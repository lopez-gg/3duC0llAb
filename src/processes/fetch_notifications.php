<?php
// Include database and session configurations
require_once __DIR__ . '/../config/db_config.php'; 
require_once __DIR__ . '/../config/session_config.php'; 

// Fetch the current user's ID from the session
$sessionUserId = $_SESSION['user_id']; // Ensure the session has the user's ID

// Function to calculate time passed (e.g., "5 days ago")
// Function to calculate time passed (e.g., "5 days ago")
function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $timeDiff = time() - $time;

    if ($timeDiff < 60) {
        return 'Just now';
    } elseif ($timeDiff < 3600) {
        $minutes = floor($timeDiff / 60);
        return $minutes . ' minute' . ($minutes === 1 ? '' : 's') . ' ago'; // Fixed here
    } elseif ($timeDiff < 86400) {
        $hours = floor($timeDiff / 3600);
        return $hours . ' hour' . ($hours === 1 ? '' : 's') . ' ago'; // Fixed here
    } elseif ($timeDiff < 604800) {
        $days = floor($timeDiff / 86400);
        return $days . ' day' . ($days === 1 ? '' : 's') . ' ago'; // Fixed here
    }
    return date('M d, Y', $time); // Show the actual date if more than 7 days ago
}

try {
    // Define the time limit for fetching recent notifications (e.g., 6 months)
    $timeLimit = date('Y-m-d H:i:s', strtotime('-6 months'));

    // Prepare query to fetch recent notifications (both general and private for the user)
    $query = "
        SELECT n.id, n.type, n.notif_content, n.created_at, n.read_at, n.status, 
               e.event_date, e.end_date 
        FROM notifications n
        LEFT JOIN events e ON n.event_id = e.id
        WHERE (n.user_id = :user_id OR n.type = 'calendar_event') 
          AND n.created_at >= :time_limit
        ORDER BY n.created_at DESC
    ";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $sessionUserId, 'time_limit' => $timeLimit]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare query to count unread notifications
    $unreadCountQuery = "
        SELECT COUNT(*) AS unread_count 
        FROM notifications 
        WHERE (user_id = :user_id OR user_id IS NULL) 
          AND status = 'unread'
          AND created_at >= :time_limit
    ";
    $unreadCountStmt = $pdo->prepare($unreadCountQuery);
    $unreadCountStmt->execute(['user_id' => $sessionUserId, 'time_limit' => $timeLimit]);
    $unreadCountResult = $unreadCountStmt->fetch(PDO::FETCH_ASSOC);
    $unreadCount = $unreadCountResult['unread_count'];

    $updatedNotifications = [];

    foreach ($notifications as &$notif) {
        // Check if the event has passed and update the notification status if needed
        if ($notif['type'] === 'calendar_event') {
            $eventPassed = false;
            $currentDate = time();

            // Only check the event dates if they exist
            if ($notif['event_date'] !== null || $notif['end_date'] !== null) {
                $eventEndDate = $notif['end_date'] ? strtotime($notif['end_date']) : null;
                $eventStartDate = strtotime($notif['event_date']);

                if ($eventEndDate && $eventEndDate < $currentDate) {
                    $eventPassed = true;
                } elseif (!$eventEndDate && $eventStartDate < $currentDate) {
                    $eventPassed = true;
                }
            }

            // Update notification status to 'past' if the event has passed
            if ($eventPassed && $notif['status'] !== 'past') {
                $updateStmt = $pdo->prepare("UPDATE notifications SET status = 'past', read_at = NOW() WHERE id = :id");
                $updateStmt->execute(['id' => $notif['id']]);
                $notif['status'] = 'past'; // Update local status
            }
        } // No else condition needed; task notifications remain 'unread'

        // Calculate how long ago the notification was created
        $notif['time_ago'] = timeAgo($notif['created_at']);

        // Add the notification to the result set
        $updatedNotifications[] = $notif;
    }

    // Return notifications and unread count as JSON response
    echo json_encode([
        'notifications' => $updatedNotifications,
        'unread_count' => $unreadCount
    ]);

} catch (PDOException $e) {
    // Log any database errors
    log_error('Failed to fetch notifications: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['error' => 'Failed to fetch notifications.']);
}
?>
