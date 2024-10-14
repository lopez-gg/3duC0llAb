<?php
//fetch_faculty_details.php
require_once __DIR__ . '/../../config/db_config.php'; 
require_once __DIR__ . '/../../config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../public/login.php');
    exit;
}
header('Content-Type: application/json');
$facultyId = $_GET['f_id'];

try {
    $query = "SELECT * FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $facultyId, PDO::PARAM_INT);
    $stmt->execute();
    
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($faculty) {
        echo json_encode($faculty); // Return user data as JSON
    } else {
        echo json_encode(['error' => 'User not found']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
