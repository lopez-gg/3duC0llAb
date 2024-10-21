<?php
require_once __DIR__ . '/../../src/config/db_config.php'; // Include your PDO setup
require_once __DIR__ . '/../../src/config/session_config.php'; // Include session configuration

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Currently logged-in user

try {
    // Start output buffering
    ob_start();

    // Get the posted data
    $request_id = $_POST['id'] ?? null;
    $new_status = $_POST['status'] ?? null;

    // Validate input
    if ($request_id === null || $new_status === null) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Fetch the change request details
    $changeRequestSql = "SELECT * FROM change_requests WHERE id = :request_id";
    $changeRequestStmt = $pdo->prepare($changeRequestSql);
    $changeRequestStmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
    $changeRequestStmt->execute();
    $changeRequest = $changeRequestStmt->fetch(PDO::FETCH_ASSOC);

    if ($changeRequest) {
        $appointment_id = $changeRequest['appointment_id'];
        $new_date = $changeRequest['new_date'];
        $new_time = $changeRequest['new_time'];

        // Update the change request status
        $updateStatusSql = "UPDATE change_requests SET status = :status WHERE id = :request_id";
        $updateStatusStmt = $pdo->prepare($updateStatusSql);
        $updateStatusStmt->bindParam(':status', $new_status, PDO::PARAM_STR);
        $updateStatusStmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);

        if ($updateStatusStmt->execute()) {
            // If the request was approved, update the appointment with the new date/time
            if ($new_status === 'approved') {
                $updateAppointmentSql = "UPDATE appointments SET appointment_date = :new_date, appointment_time = :new_time, status = :status WHERE id = :appointment_id";
                $updateAppointmentStmt = $pdo->prepare($updateAppointmentSql);
                $updateAppointmentStmt->bindParam(':new_date', $new_date);
                $updateAppointmentStmt->bindParam(':new_time', $new_time);
                $updateAppointmentStmt->bindParam(':status', $new_status);
                $updateAppointmentStmt->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT); 
                $updateAppointmentStmt->execute();
            }

            // Send success response
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update change request status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Change request not found.']);
    }

    // End output buffering and flush the output
    ob_end_flush();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    ob_end_flush();
}
?>
