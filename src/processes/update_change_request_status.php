<?php
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/session_config.php';

header('Content-Type: application/json');

try {
    // Start output buffering
    ob_start();

    $request_id = $_POST['id'];   
    $new_status = $_POST['status']; 

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
            // If approved, update the corresponding appointment
            if ($new_status === 'approved') {
                $updateAppointmentSql = "UPDATE appointments SET appointment_date = :new_date, appointment_time = :new_time WHERE id = :appointment_id";
                $updateAppointmentStmt = $pdo->prepare($updateAppointmentSql);
                $updateAppointmentStmt->bindParam(':new_date', $new_date);
                $updateAppointmentStmt->bindParam(':new_time', $new_time);
                $updateAppointmentStmt->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
                $updateAppointmentStmt->execute();
            }

            // Send success response
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update change request']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Change request not found']);
    }

    // End output buffering and flush the output
    ob_end_flush();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    ob_end_flush();
}
?>
