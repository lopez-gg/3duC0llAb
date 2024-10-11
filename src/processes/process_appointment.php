<?php
// process_appointment.php
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../public/login.php');
    exit;
}

// Check if form data is set
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $facultyId = $_POST['faculty_id']; 
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];
    $additionalNotes = $_POST['additional_notes'];
    $faculty_username = $_POST['faculty_username'];

    // Validate form data (you can add more validation as needed)
    if (empty($appointmentDate) || empty($appointmentTime)) {
        // Redirect back with error message
        $_SESSION['error'] = 'Please fill in all required fields.';
        header('Location: ../../public/user/request_appointment.php?f_id=' . $facultyId);
        exit;
    }

    try {
        // Prepare the SQL statement to insert the appointment request
        $query = "INSERT INTO appointments (user_id, faculty_id, appointment_date, appointment_time, additional_notes) 
                  VALUES (:user_id, :faculty_id, :appointment_date, :appointment_time, :additional_notes)";
        $stmt = $pdo->prepare($query);

        // Bind parameters
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':faculty_id', $facultyId, PDO::PARAM_INT);
        $stmt->bindParam(':appointment_date', $appointmentDate);
        $stmt->bindParam(':appointment_time', $appointmentTime);
        $stmt->bindParam(':additional_notes', $additionalNotes, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            // Create a notification for the faculty
            $notifType = 'info'; // Type of notification
            $notifContent = "You have received a new appointment request from User ID: $userId.";
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
            $_SESSION['success'] = 'Appointment request submitted successfully!';
            // Redirect to a success page or back to the request page
            header('Location: ../../public/user/request_appointment.php'); // Redirect back to request page
            exit;
        } else {
            // Handle insert error
            $_SESSION['error'] = 'Failed to submit appointment request. Please try again.';
            header('Location: ../../public/user/request_appointment.php?f_id=' . $faculty_username);
            exit;
        }
    } catch (PDOException $e) {
        // Log error
        log_error('Database error: ' . $e->getMessage(), 'appointment_errors.txt');
        $_SESSION['error'] = 'An error occurred. Please try again later.';
        header('Location: ../../public/user/request_appointment.php?f_id=' . $faculty_username);
        exit;
    }
} else {
    // If the request method is not POST, redirect back
    header('Location: ../../../public/login.php');
    exit;
}
