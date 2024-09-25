<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? null;
    $facultyId = $_POST['faculty_id'] ?? null;

    if ($action === 'edit' && $facultyId) {
        header("Location: ../../../public/admin/edit_faculty.php?id=" . intval($facultyId));
        exit;
    }

    if ($action === 'deactivate' && $facultyId) {
        // Deactivate faculty member
        $stmt = $pdo->prepare("UPDATE users SET status = 'deactivated' WHERE id = :id");
        $stmt->execute([':id' => $facultyId]);

        // Optionally, you can set a success message
        $_SESSION['success_title'] = 'Success';
        $_SESSION['success_message'] = 'Faculty member deactivated successfully.';
        header("Location: ../../../public/admin/faculty.php");
        exit;
    }

    if ($action === 'activate' && $facultyId) {
        // Activate faculty member
        $stmt = $pdo->prepare("UPDATE users SET status = 'active' WHERE id = :id");
        $stmt->execute([':id' => $facultyId]);

        // Set a success message
        $_SESSION['success_title'] = 'Success';
        $_SESSION['success_message'] = 'Faculty member activated successfully.';
        header("Location: ../../../public/admin/faculty.php");
        exit;
    }
}

// If the request method is not POST, redirect back
header("Location: ../../../public/admin/faculty.php");
exit;
?>
