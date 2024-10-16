<?php
// process_appointment.php
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit;
}

// Check if form data is set
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $facultyId = $_POST['faculty_id'];
    $appointmentTitle = $_POST['appointment_title']; 
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];
    $additionalNotes = $_POST['additional_notes'];
    $faculty_username = $_POST['faculty_username'];
    $utype = $_SESSION['accType'];
    $my_username = $_SESSION['username'];

    // Validate form data (you can add more validation as needed)
    if (empty($appointmentDate) || empty($appointmentTime) || empty($appointmentTitle)) {
        // Redirect back with error message
        $_SESSION['success_message'] = 'Please fill in all required fields.';
        if($utype === 'ADMIN'){
            header("Location: ../../public/admin/request_appointments.php");
        }else if($acctype === 'USER'){
            header("Location: ../../public/user/request_appointments.php");
        }
        exit;
    }
    try {
        // Prepare the SQL statement to insert the appointment request
        $query = "INSERT INTO appointments (requestor, requestee, appointment_title, appointment_date, appointment_time, additional_notes) 
                  VALUES (:requestor, :requestee, :appointment_title, :appointment_date, :appointment_time, :additional_notes)"; // Fixed here
        $stmt = $pdo->prepare($query);
    
        // Bind parameters
        $stmt->bindParam(':requestor', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':requestee', $facultyId, PDO::PARAM_INT);
        $stmt->bindParam(':appointment_title', $appointmentTitle, PDO::PARAM_STR); // Added here
        $stmt->bindParam(':appointment_date', $appointmentDate);
        $stmt->bindParam(':appointment_time', $appointmentTime);
        $stmt->bindParam(':additional_notes', $additionalNotes, PDO::PARAM_STR);
    
        // Execute the statement
        if ($stmt->execute()) {
            // Create a notification for the faculty
            $notifType = 'appointment_request'; 
                $notifContent = "You have received a new appointment request titled '$appointmentTitle' from $my_username";
                $notifStatus = 'unread';

            // Prepare notification SQL
            $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, type, notif_content, status, created_at) 
                                         VALUES (:user_id, :type, :notif_content, :status, NOW())");
            $notifStmt->bindParam(':user_id', $facultyId); // Notify the faculty member
            $notifStmt->bindParam(':type', $notifType);
            $notifStmt->bindParam(':notif_content', $notifContent);
            $notifStmt->bindParam(':status', $notifStatus);
            $notifStmt->execute();

            // Set session success message
            $_SESSION['success_title'] = 'Request Sent!';
            $_SESSION['success_message'] = 'Appointment request submitted successfully!';
            // Redirect to a success page or back to the request page
            if($utype === 'ADMIN'){
                header("Location: ../../public/admin/request_appointment.php");
            }else if($utype === 'USER'){
                header("Location: ../../public/user/request_appointment.php");
            }
            exit;
        } else {
            // Handle insert error
            $_SESSION['success_message'] = 'Failed to submit appointment request. Please try again.';
            header('Location: ../../public/user/request_appointment.php?f_id=' . $faculty_username);
            exit;
        }
    } catch (PDOException $e) {
        // Log error
        log_error('Database error: ' . $e->getMessage(), 'appointment_errors.txt');
        $_SESSION['success_message'] = 'An error occurred. Please try again later.';
        //  Redirect to a success page or back to the request page
         if($utype === 'ADMIN'){
            header("Location: ../../public/admin/request_appointment.php");
        }else if($utype === 'USER'){
            header("Location: ../../public/user/request_appointment.php");
        }
        exit;
    }
} else {
     if($utype === 'ADMIN'){
        header("Location: ../../public/admin/appointments.php");
    }else if($utype === 'USER'){
        header("Location: ../../public/user/appointments.php");
    }
    exit;
}
