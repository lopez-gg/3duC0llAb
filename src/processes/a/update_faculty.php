<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';
require_once __DIR__ . '/../../config/access_control.php';

// Check user access
check_access('ADMIN');

// Check if ID is set in the query string
if (!isset($_GET['id'])) {
    $_SESSION['success_message'] = "ID is missing from the request.";
    header("Location: faculty.php");
    exit;
}

// Fetch faculty member details
$facultyId = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $facultyId]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    $_SESSION['success_message'] = "Faculty member doesn't exist.";
    header("Location: ../../../public/admin/faculty.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update faculty member details
    $username = $_POST['username'] ?? $faculty['username'];
    $firstname = $_POST['firstname'] ?? $faculty['firstname'];
    $lastname = $_POST['lastname'] ?? $faculty['lastname'];
    $gradeLevel = $_POST['gradeLevel'] ?? $faculty['gradeLevel'];
    $section = $_POST['section'] ?? $faculty['section'];
    $status = $_POST['status'] ?? $faculty['status'];

    $stmt = $pdo->prepare("
        UPDATE users 
        SET username = :username, firstname = :firstname, lastname = :lastname, 
            gradeLevel = :gradeLevel, section = :section, status = :status 
        WHERE id = :id
    ");
    
    $stmt->execute([
        ':username' => $username,
        ':firstname' => $firstname,
        ':lastname' => $lastname,
        ':gradeLevel' => $gradeLevel,
        ':section' => $section,
        ':status' => $status,
        ':id' => $facultyId
    ]);

    // Set success message and redirect
    $_SESSION['success_message'] = "Faculty member updated successfully.";
    header("Location: ../../../public/admin/faculty.php");
    exit;
}

// Redirect if accessed directly without POST
header("Location: ../../../public/admin/faculty.php");
exit;
