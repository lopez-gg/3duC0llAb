<?php
require_once __DIR__ . '/../../config/db_config.php'; // Database configuration file
require_once __DIR__ . '/../../config/config.php';    // Application-specific configurations

session_start(); // Start the session

if(!(isset($_SESSION['user_id']))){
    header('Location: ../../../public/login.php');
    exit;
}

try {
    // Correcting the typo in your query (SELCT -> SELECT)
    $query = "SELECT * FROM users"; 

    // Prepare the query
    $stmt = $pdo->prepare($query); 
    $stmt->execute();

    // Fetch the resulting faculties
    $faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the faculties as JSON
    echo json_encode($faculties);

} catch (PDOException $e) {
    // Log the error and return a JSON error response
    log_error('Error fetching faculties: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['error' => 'Failed to fetch faculties']);
}

?>
