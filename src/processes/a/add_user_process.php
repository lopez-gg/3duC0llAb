<?php
require_once __DIR__ . '/../../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../../config/config.php'; // Include global configuration

// Get form data
$username = $_POST['username'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$gradeLevel = $_POST['gradeLevel'];
$section = $_POST['section'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
$status = $_POST['status'];
$accType = $_POST['accType'];

try {
    // Prepare and execute the SQL statement
    $stmt = $pdo->prepare("INSERT INTO users (username, firstname, lastname, gradeLevel, section, password, status, accType) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$username, $firstname, $lastname, $gradeLevel, $section, $password, $status, $accType]);
    echo "New record created successfully";
} catch (PDOException $e) {
    // Log error to file using the function from config.php
    log_error('Error adding new user: ' . $e->getMessage(), 'user_errors.txt');
    echo 'An error occurred. Please try again later.';
}
?>
