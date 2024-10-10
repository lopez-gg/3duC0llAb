<?php
//fetch_faculty_details.php
require_once __DIR__ . '/../../config/db_config.php'; // Database configuration

if (!isset($_GET['user_id'])) {
    header('Location: ../../../public/login.php');
    exit;
}
header('Content-Type: application/json');
$userId = $_GET['user_id'];

try {
    $query = "SELECT * FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode($user); // Return user data as JSON
    } else {
        echo json_encode(['error' => 'User not found']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
