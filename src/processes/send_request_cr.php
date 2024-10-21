<?php
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/session_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $new_date = $_POST['new_date'] ?? null;
    $new_time = $_POST['new_time'] ?? null;
    $notes = $_POST['notes'] ?? null;
    $user_id = $_SESSION['user_id']; 
    $utype = $_SESSION['accType']; 

    // Get the request owner (the user who sent the original appointment request)
    $appointment_sql = "SELECT requestor, requestee, appointment_title FROM appointments WHERE id = :appointment_id";
    $appointment_stmt = $pdo->prepare($appointment_sql);
    $appointment_stmt->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
    $appointment_stmt->execute();
    $appointment = $appointment_stmt->fetch(PDO::FETCH_ASSOC);

    if ($appointment) {
        $request_owner = $appointment['requestor'];
        if ($request_owner === $user_id){
            $reciever = $appointment['requestee'];
        }else if($request_owner != $user_id){
            $reciever = $request_owner;
        }
        $appointment_title = $appointment['appointment_title'];

        // Update the change request in the change_requests table
        $sql = "UPDATE change_requests 
                SET new_date = :new_date, new_time = :new_time, requested_by = :requested_by, 
                    requested_to = :requested_to, notes = :notes, status = 'pending' 
                WHERE appointment_id = :appointment_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
        $stmt->bindParam(':new_date', $new_date);
        $stmt->bindParam(':new_time', $new_time);
        $stmt->bindParam(':requested_by', $user_id, PDO::PARAM_INT); 
        $stmt->bindParam(':requested_to', $request_owner, PDO::PARAM_INT); 
        $stmt->bindParam(':notes', $notes);

        if ($stmt->execute()) {
            // Create notification content
            $notif_content = "A new request to change the appointment '$appointment_title' has been made. New Date: $new_date, New Time: $new_time.";

            // Insert a notification for the request_owner
            $notif_sql = "INSERT INTO notifications (user_id, type, notif_content, event_id) 
                          VALUES (:user_id, 'appointment', :notif_content, :event_id)";
            $notif_stmt = $pdo->prepare($notif_sql);
            $notif_stmt->bindParam(':user_id', $reciever, PDO::PARAM_INT);
            $notif_stmt->bindParam(':notif_content', $notif_content, PDO::PARAM_STR);
            $notif_stmt->bindParam(':event_id', $appointment_id, PDO::PARAM_INT);

            // Execute the notification insert
            $notif_stmt->execute();

            $_SESSION['success_message'] = 'Appointment change request sent!';
            // Redirect based on the user type
            if ($utype === 'ADMIN') {
                header("Location: ../../public/admin/appointments.php");
            } else if ($utype === 'USER') {
                header("Location: ../../public/user/appointments.php");
            }
            exit(); // Prevent further script execution after redirect
        } else {
            $_SESSION['error_message'] = 'Failed to send appointment change request. Please try again.';
            if ($utype === 'ADMIN') {
                header("Location: ../../public/admin/appointments.php");
            } else if ($utype === 'USER') {
                header("Location: ../../public/user/appointments.php");
            }
            exit(); // Prevent further script execution after redirect
        }
    } else {
        // Error handling for appointment not found
        $_SESSION['error_message'] = 'Appointment not found.';
        if ($utype === 'ADMIN') {
            header("Location: ../../public/admin/appointments.php");
        } else if ($utype === 'USER') {
            header("Location: ../../public/user/appointments.php");
        }
        exit();
    }
}
?>
