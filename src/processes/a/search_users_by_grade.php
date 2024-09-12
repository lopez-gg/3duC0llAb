<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';



if (isset($_SESSION['grade']) && isset($_POST['query'])) {

    if (strtolower($_SESSION['grade']) === 'sned') {
        $grade = 'SNED'; // Use the exact format stored in the database
    } else {
        $grade = 'Grade ' . (string)$_SESSION['grade']; // Format like 'Grade 4'
    }
      // Retrieve grade level from session
    $query = '%' . $_POST['query'] . '%'; // Add wildcards for partial matching

    try {
        
    echo "Grade: $grade <br>";
    echo "Query: $query <br>";
        // Prepare SQL query to search users within the specified grade
        $stmt = $pdo->prepare("
            SELECT id, username, firstname, lastname 
            FROM users 
            WHERE gradeLevel = :grade 
            AND (username LIKE :query OR firstname LIKE :query OR lastname LIKE :query)
        ");
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR); // Bind the grade level
        $stmt->bindParam(':query', $query, PDO::PARAM_STR); // Bind the query
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users) {
            foreach ($users as $user) {
                echo '<div class="search-result-item" data-userid="' . htmlspecialchars($user['id']) . '">'
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
    echo '<div class="search-result-item">No query or grade level provided</div>';
}
?>
