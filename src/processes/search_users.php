<?php
require_once __DIR__ . '/../config/db_config.php'; // Include database config
require_once __DIR__ . '/../config/session_config.php';

$userId = $_SESSION['user_id'];

if (isset($_POST['query'])) {
    $query = '%' . $_POST['query'] . '%'; // Add wildcards for partial matching
    $isMessaging = isset($_POST['isMessaging']) && $_POST['isMessaging'] == 'true'; // Check if messaging request

    try {
        // Prepare the base SQL statement
        $sql = "SELECT id, username, firstname, lastname, gradeLevel, section FROM users WHERE (username LIKE :query OR firstname LIKE :query OR lastname LIKE :query)";
        
        // If this is a messaging search, exclude the current user
        if ($isMessaging) {
            $sql .= " AND id != :currentUserId";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':query', $query);

        // Bind the current user ID if it's a messaging search
        if ($isMessaging) {
            $stmt->bindParam(':currentUserId', $userId);
        }

        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {
            // Wrap the users in a response object
            $response = ['users' => $users];
        } else {
            $response = ['users' => [], 'message' => 'No users found'];
        }
        echo json_encode($response);
        
    } catch (Exception $e) {
        // Log any errors
        log_error('User search failed: ' . $e->getMessage(), 'error.log');
        echo '<div class="search-result-item">Error occurred. Please try again.</div>';
    }
} else {
    echo '<div class="search-result-item">No query provided</div>';
}
?>
