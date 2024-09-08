
<?php
require_once __DIR__ . '/../config/db_config.php'; // Include database config

if (isset($_POST['query'])) {
    $query = '%' . $_POST['query'] . '%'; // Add wildcards for partial matching

    try {
        $stmt = $pdo->prepare("SELECT id, username, firstname, lastname FROM users WHERE username LIKE :query OR firstname LIKE :query OR lastname LIKE :query");
        $stmt->bindParam(':query', $query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {
            foreach ($users as $user) {
                echo '<div class="search-result-item" data-userid="' . $user['id'] . '">'
                     . htmlspecialchars($user['username']) . ' - ' 
                     . htmlspecialchars($user['firstname']) . ' ' 
                     . htmlspecialchars($user['lastname']) . 
                     '</div>';
            }
        } else {
            echo '<div class="search-result-item">No users found</div>';
        }
    } catch (Exception $e) {
        // Log any errors
        log_error('User search failed: ' . $e->getMessage(), 'error.log');
        echo '<div class="search-result-item">Error occurred. Please try again.</div>';
    }
} else {
    echo '<div class="search-result-item">No query provided</div>';
}
?>
