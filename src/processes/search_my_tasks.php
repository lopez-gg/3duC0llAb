<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';

// Check if a search term is provided
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    $userID = $_SESSION['user_id']; 

    // Prepare the SQL statement
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE (assignedBy = :user_id AND assignedTo = :user_id) OR assignedTo = :user_id  AND title LIKE :searchTerm");
    $searchTermParam = '%' . $searchTerm . '%';
    $stmt->bindParam(':user_id', $userID);
    $stmt->bindParam(':searchTerm', $searchTermParam);
    
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    header('Content-Type: application/json');
    echo json_encode($tasks);
    exit;
}
?>
