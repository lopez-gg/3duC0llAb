<?php
require_once __DIR__ . '/../../config/db_config.php'; 
require_once __DIR__ . '/../../config/config.php'; 
require_once __DIR__ . '/../../config/session_config.php';

ini_set('log_errors', 1);
error_reporting(E_ALL);

// Get form data
$username = $_POST['username'];
$firstname = $_POST['firstname'];
$middlename = $_POST['middlename'];
$lastname = $_POST['lastname'];
$gradeLevel = $_POST['gradeLevel'];
$section = $_POST['section'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
$userID = $_SESSION['user_id'];


$query = "SELECT id FROM users WHERE username = :username AND gradeLevel = :grade";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->bindParam(':grade', $gradeLevel, PDO::PARAM_STR);
$stmt->execute();
$registeredID = $stmt->fetch(PDO::FETCH_ASSOC);

if ($registeredID){
    $_SESSION['success_message'] = 'This faculty member hes already been registered.';
    header('Location: ../../../public/admin/add_user.php');
}else{
    try {
        // Prepare and execute the SQL statement
        $stmt = $pdo->prepare("INSERT INTO users (username, firstname, middlename, lastname, gradeLevel, section, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $firstname, $middlename, $lastname, $gradeLevel, $section, $password]);

        $query = "SELECT id FROM users WHERE username = :username AND gradeLevel = :grade";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':grade', $gradeLevel, PDO::PARAM_STR);
        $stmt->execute();
        $registeredID = $stmt->fetch(PDO::FETCH_ASSOC);
        $theId = $registeredID['id'];

        $activity_message = '[ADDED] New faculty member: "' . $firstname . ' ' . $middlename . ' ' . $lastname . '" in ' . $gradeLevel;
        add_activity_history($userID, $theId, $activity_message);

        echo $_SESSION['success_message'] = "New user registered successfully";
        header("Location: ../../../public/admin/add_user.php");
        exit;
    } catch (PDOException $e) {
        // Log error to file using the function from config.php
        log_error('Error adding new user: ' . $e->getMessage(), 'db_errors.txt');
        echo $_SESSION['error_message'] = 'An error occurred. Please try again later.';
        header("Location: ../../../public/admin/add_user.php");
        exit;
    }
}
?>
