<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/check_new_messages.php';


$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 

// Check if the user is user
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); 
    exit;
}

$userId = ($_SESSION['user_id']);
$dashb = '';
$my_space = '';
$calendr = 'calendar';
$gen_forum ='';

// Set timezone
date_default_timezone_set('Asia/Manila'); 

// Get current date and time
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$currentMonth = date('F Y'); // e.g., July 2024
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link href='https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet' />
    <link href="../../src/css/custom-calendar.css" rel="stylesheet" />
    <link href="../../src/css/gen.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../src/css/message.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/jquery.min.js'></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/lib/moment.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src='https://fullcalendar.io/releases/fullcalendar/3.9.0/fullcalendar.min.js'></script>

    
</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>
        <div class="content" id="content">
        
            <!-- Calendar display -->
            <h2>PSCS Calendar</h2>
            <button><a href="manage_events.php">Manage Events</a></button>

            <div id='calendar'></div>

            <!-- Modal for Event Details -->
            <div class="modal fade" id="eventDetailsModal" tabindex="-1" role="dialog" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="c-modal-content">
                        <div class="c-modal-header">
                            <h5 class="c-modal-title" id="eventDetailsModalLabel">Event Details</h5>
                        </div>
                        <div class="c-modal-body">
                            <!-- Event details will be populated here -->
                        </div>
                        <div class="c-modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
        <?php include '../../src/config/js_custom_scripts.php';?>
    </div>

    

</body>
</html>
