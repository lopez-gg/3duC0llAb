<?php
require_once __DIR__ . '/../../config/db_config.php'; 
require_once __DIR__ . '/../../config/config.php'; 
require_once __DIR__ . '/../../config/session_config.php';

// Get form data
$username = $_POST['username'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$gradeLevel = $_POST['gradeLevel'];
$section = $_POST['section'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

try {
    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, gradeLevel, section, password) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $firstname, $lastname, $gradeLevel, $section, $password]);
    $_SESSION['success_message'] = "New record created successfully";
    header("Location: ../../../public/admin/add_user.php");
    exit;
} catch (PDOException $e) {
    // Log error to file using the function from config.php
    log_error('Error adding new user: ' . $e->getMessage(), '../../../logs/error.log');
    $_SESSION['error_message'] = 'An error occurred. Please try again later.';
    header("Location: ../../public/admin/add_user.php");
    exit;
}
?>
