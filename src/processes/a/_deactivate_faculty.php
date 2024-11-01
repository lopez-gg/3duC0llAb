<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $userID = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET status = 'deactivated' WHERE id = :id");
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {

            $query = "SELECT username FROM users WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
            $faculty_username = $faculty['username'];

            $activity_message = '[UPDATED] Account status for: "' . $faculty_username . '" as DEACTIVATED.';
            add_activity_history($userID, $id, $activity_message);
            
            $_SESSION['success_title'] = 'Success';
            $_SESSION['success_message'] = 'Faculty member deactivated successfully.';
            // header('Location: /public/faculty.php');
            exit;
        } else {
            throw new Exception('Error deactivating faculty member.');
        }
    } catch (Exception $e) {
        // Log error and set session for error message
        log_error('Error: ' . $e->getMessage(), 'deactivate_error.log');
        
        $_SESSION['success_title'] = 'Failed';
        $_SESSION['verification_message'] = 'Error deactivating faculty member. Please try again.';
        // header('Location: /public/faculty.php');
        exit;
    }
}
