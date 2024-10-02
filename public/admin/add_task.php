<!-- add_personal_task.php -->
<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/check_new_messages.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// Check if the user is admin
check_access('ADMIN');
$dashb = '';
$my_space = 'my_space';
$calendr = '';
$gen_forum ='';
// Set timezone
date_default_timezone_set('Asia/Manila'); 

// Get current date and time
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$currentMonth = date('F');
$currentYear = date('Y');

$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Personal Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/form.css">
    <link rel="stylesheet" href="../../src/css/message.css">
</head>
<body>
    <?php include '../nav-sidebar-temp.php'; ?>
    
    <div class="content" id="content">
        <h2>Create Personal Task</h2>
        
        <div class="form-container"> 
            <form action="../../src/processes/a/process_add_personal_task.php" method="POST">
                <!-- Task Title -->
                <div class="form-group">
                    <label for="title">Task Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <!-- Task Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>

                <!-- Urgency Selection -->
                <div class="t-urgency-container">
                    <label class='t-urgency-e'>
                        <input type='radio' name='urgency' value='Normal' default/> Normal
                    </label>
                    <label class='t-urgency-e'>
                        <input type='radio' name='urgency' value='Urgent'/> Urgent
                    </label>   
                    <label class='t-urgency-e'>
                        <input type='radio' name='urgency' value='Important'/> Important
                    </label>
                    <label class='t-urgency-e'>
                        <input type='radio' name='urgency' value='Urgent and Important'/> Urgent and Important
                    </label>
                </div>

                <!-- Due Date and Time -->
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                </div>
                <div class="form-group">
                    <label for="due_time">Due Time</label>
                    <input type="time" class="form-control" id="due_time" name="due_time" required>
                </div>

                <!-- Hidden Fields -->
                <input type="hidden" name="assignedTo" value="<?php echo $_SESSION['user_id']; ?>"> <!-- Auto-assign to the admin -->
                <input type="hidden" name="taskType" value="private"> <!-- This is a personal task -->
                <input type="hidden" name="assignedBy" value="<?php echo $_SESSION['user_id']; ?>">

                <!-- Submit and Cancel Buttons -->
                <button type="submit" class="btn btn-primary">Add Task</button>
                <button type="button" class="btn btn-danger" 
                        onclick="openVerificationModal('cancel_form_', 'Cancel', 'All entries will be discarded. Are you sure you want to cancel?', 'Yes', 'my_space.php', '1')">Cancel
                </button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <?php include '../../src/config/js_custom_scripts.php';?>
</body>
</html>
