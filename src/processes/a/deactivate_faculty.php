<?php
require_once __DIR__ . '/../../config/db_config.php';

session_start(); // Ensure session is started

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET status = 'deactivated' WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $_SESSION['success_title'] = 'Success';
            $_SESSION['success_message'] = 'Faculty member deactivated successfully.';
            header('Location: /public/faculty.php');
            exit;
        } else {
            throw new Exception('Error deactivating faculty member.');
        }
    } catch (Exception $e) {
        // Log error and set session for error message
        log_error('Error: ' . $e->getMessage(), 'deactivate_error.log');
        $_SESSION['verification_message'] = 'Error deactivating faculty member. Please try again.';
        header('Location: /public/faculty.php');
        exit;
    }
}
