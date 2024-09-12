<?php
require_once __DIR__ . '/../../src/config/access_control.php'; // Include access control script
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 


//Check if the user is an admin
check_access('ADMIN');

$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
include '../display_mod.php';
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/event_form.css">

    <title>Add New User</title>
</head>
<body>

    <div class="top-nav">
        <div class="left-section">
            <button class="sidebar-toggle-button" onclick="toggleSidebar()">☰</button>
            <div class="app-name">EduCollab</div>
        </div>

        <!-- Bell icon with notification count -->
        <div class="notification-bell">
            <i class="bi bi-bell-fill"></i>
            <span class="notification-count">0</span>
        </div>
        
        <!-- Notification dropdown -->
        <div class="notification-dropdown">
            <ul class="notification-list">
                <!-- Notifications will be appended here by JavaScript -->
            </ul>
            <button class="see-more" style="display: none;">See More...</button>
        </div>

        <div class="user-profile" id="userProfile">
            <div class="user-icon" onclick="toggleDropdown()">U</div>
            <div class="dropdown" id="dropdown">
                <a href="#">Settings</a>
                <form action="../../src/processes/logout.php" method="post">
                    <input type="submit" name="logout" value="Logout">
                </form>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="sidebar" id="sidebar">
            <div class="logo"></div> 
            <div class="nav-links">
                <a href="dashboard.php#">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div>

        <div class="content" id="content">
        <h1>Add New User</h1>
        <form action="../../src/processes/a/add_user_process.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required><br><br>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required><br><br>

            <label for="gradeLevel">Grade Level:</label>
            <input type="text" id="gradeLevel" name="gradeLevel" required><br><br>

            <label for="section">Section:</label>
            <input type="text" id="section" name="section" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Add User">
            
        </form>
        
        <button type="button" class="btn btn-danger" onclick="openVerificationModal('cancel_form_', 'Cancel', 'All entries will be discarded. Are you sure you want to cancel?  ', 'Yes', 'dashboard.php', '1')">Cancel</button>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>

    
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/verify.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
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
