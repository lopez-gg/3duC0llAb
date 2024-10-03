<?php
// remind_me.php
require_once __DIR__ . '/../../config/db_config.php'; 
require_once __DIR__ . '/../../config/session_config.php'; 

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve task ID and reminder details from the POST request
    $acctype = htmlspecialchars($_POST['utyp'] ?? '');
    $taskId = htmlspecialchars($_POST['id'] ?? '');
    $reminderDate = htmlspecialchars($_POST['reminder_date'] ?? '');
    $reminderMessage = htmlspecialchars($_POST['reminder_message'] ?? '');
    $reminderType = htmlspecialchars($_POST['rtype'] ?? '');

    var_dump($_POST);
    
    // Optional: Validate task ID and reminder date are not empty
    if (empty($taskId) || empty($reminderDate) || empty($acctype) || empty($reminderType)) {
        echo 'Task ID and reminder date are required.';
        echo 'taskId: ' . $taskId;
        echo 'reminderdate: ' . $reminderDate;
        echo 'acctype: ' . $acctype;
        echo 'rtype: ' . $reminderType;
        exit;
    }

    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo 'You must be logged in to set a reminder.';
        exit;
    }

    try {
        // Prepare SQL statement to insert the reminder
        $stmt = $pdo->prepare("INSERT INTO reminders (task_id, user_id, reminder_type, reminder_date, reminder_message, status) 
                               VALUES (:task_id, :user_id, :reminder_type, :reminder_date, :reminder_message, 'waiting')");
        // Bind parameters
        $stmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':reminder_type', $reminderType, PDO::PARAM_STR);
        $stmt->bindParam(':reminder_date', $reminderDate, PDO::PARAM_STR); // Assuming 'YYYY-MM-DD HH:MM:SS' format
        $stmt->bindParam(':reminder_message', $reminderMessage, PDO::PARAM_STR);

        // Execute the statement
        if ($stmt->execute()) {
            if($acctype === 'am'){
                echo 'Reminder has been successfully saved.';
                // Optionally redirect back or to another page
                $_SESSION['success_title'] = 'Success!';
                $_SESSION['success_message'] = 'Reminder added!';
                // header("Location: ../../../public/admin/my_space.php");
            }else if($acctype === 'ur'){
                echo 'Reminder has been successfully saved.';
                // Optionally redirect back or to another page
                $_SESSION['success_title'] = 'Success!';
                $_SESSION['success_message'] = 'Reminder added!';
                // header("Location: ../../../public/user/my_space.php");
            }
        } else {
            // echo 'Failed to save the reminder. Please try again.';
        }

    } catch (PDOException $e) {
        // Log error and show a generic error message
        log_error('Failed to save reminder: ' . $e->getMessage(), 'db_errors.txt');
        // echo 'An error occurred while saving the reminder. Please try again later.';
        $_SESSION['success_title'] = 'Failed';
        $_SESSION['success_message'] = 'Failed to add reminder. Please try again.';
        header("Location: ../../../public/admin/my_space.php");
    }

} else {
    echo 'Invalid request method.';
}
?>
