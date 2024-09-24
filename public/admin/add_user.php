<?php
require_once __DIR__ . '/../../src/config/access_control.php'; // Include access control script
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 


//Check if the user is an admin
check_access('ADMIN');

$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
include '../display_mod.php';
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/event_form.css">

    <title>Add New User</title>
</head>
<body>

    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">

            <h1>Add New Account</h1>

            <form action="../../src/processes/a/add_user_process.php" method="post">
                <div class="user-form-group">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" required><br><br>
                    </div>
                    <div class="form-group">
                        <label for="firstname">First Name:</label>
                        <input type="text" id="firstname" name="firstname" required><br><br>
                    </div>
                    <div class="form-group">
                        <label for="lastname">Last Name:</label>
                        <input type="text" id="lastname" name="lastname" required><br><br>
                    </div>
                    <div class="form-group">
                        <label for="gradeLevel">Grade Level:</label>
                        <select name="gradeLevel" id="gradeLevel">
                            <option value="Grade 1">Grade 1</option>
                            <option value="Grade 2">Grade 2</option> 
                            <option value="Grade 3">Grade 3</option> 
                            <option value="Grade 4">Grade 4</option> 
                            <option value="Grade 5">Grade 5</option>
                            <option value="Grade 6">Grade 6</option> 
                            <option value="SNED">SNED</option>          
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="section">Section:</label>
                        <input type="text" id="section" name="section" value="Subject Teacher"><br><br>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required><br><br>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
                
            </form>
            
            <button type="button" class="btn btn-secondary" onclick="openVerificationModal('', '', 'Are you sure you want to discard changes?', 'Discard', 'faculty.php', '1')">Cancel</button>
    
        </div>
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/verify.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
    <script src='../../src/js/datetime.js'></script>
    <script>
        $(window).on('load', function() {
            <?php if ($successMessage): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 4500);
            <?php endif; ?>
        });
    </script>
</body>
</html>
