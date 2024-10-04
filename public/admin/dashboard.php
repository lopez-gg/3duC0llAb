<?php
// dashboard.php
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}

$userId = ($_SESSION['user_id']);
$dashb = 'dashboard';

// Set timezone
date_default_timezone_set('Asia/Manila'); 

// Get current date and time
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$currentMonth = date('F');
$currentYear = date('Y');
$currentDate = new DateTime();
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/a/dashb.css">
    <link rel="stylesheet" href="../../src/css/message.css">
    <link rel="stylesheet" href="../../src/css/tasks.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 

    <title>Dashboard</title>

</head>
<body>
    <?php require_once __DIR__ . '/../display_archived_task_alert.php';?>
    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
        
            <section class='main-sec' id='sec-one'>
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            </section>

            <hr>
                
            <section class='main-sec' id='sec-two'>
                <a href="faculty.php"><div class="space">Faculty</div></a>
                <a href="general_forum.php"><div class="space">General Forum</div></a>
                <!-- <a href="#"><div class="space">Appointment Requests</div></a> -->
            </section>

            <hr>


            <section class='main-sec' id='sec-three'> 
                <div class="d-events-main-container">
                    <h1 id="events-heading"><?php echo date('F'); ?> Events</h1>
                    <div class="d-events-list-container">
                        <ul id="upcoming-events">
                            <?php if (!empty($events)): ?>
                                <?php foreach ($events as $event): ?>
                                    <?php
                                    $eventStartDate = new DateTime($event['event_date']);
                                    $eventEndDate = new DateTime($event['end_date']);
                                    
                                    // Determine event status based on $currentDate
                                    $status = '';
                                    if ($eventStartDate > $currentDate) {
                                        $status = 'Upcoming';
                                    } elseif ($eventEndDate < $currentDate) {
                                        $status = 'Ended';
                                    } elseif ($eventStartDate->format('Y-m-d') === $currentDate->format('Y-m-d')) {
                                        $status = 'Today';
                                    }
                                    ?>
                                    <li>
                                        <a href='calendar.php'>
                                            <div class='space event-card'>
                                                <div class='event-info'>
                                                    <div class='event-date'>
                                                        <?php echo $eventStartDate->format('d'); // Display the event day ?>
                                                    </div>
                                                    <div class='event-details'>
                                                        <strong><?php echo $event['title']; ?></strong><br>
                                                        <p class='event-status'><?php echo $status; ?></p>
                                                        <p class='event-description'>
                                                            <?php echo substr($event['description'], 0, 50) . (strlen($event['description']) > 50 ? '...' : ''); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No events for this month.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </section>



            <hr>
            
            <section class='main-sec' id='sec-four'>
                <a href='space_home.php?grade=1'><div class="space">
                    Grade 1
                </div></a>
                <a href='space_home.php?grade=2'><div class="space">
                    Grade 2
                </div></a>
                <a href='space_home.php?grade=3'><div class="space">
                    Grade 3
                </div></a>
                <a href='space_home.php?grade=4'><div class="space">
                    Grade 4
                </div></a>
                <a href='space_home.php?grade=5'><div class="space">
                    Grade 5
                </div></a>
                <a href='space_home.php?grade=6'><div class="space">
                    Grade 6
                </div></a>
                <a href='space_home.php?grade=SNED'><div class="space">
                    SNED
                </div></a>

            </section>

        </div>

    </div>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
    <script src='../../src/js/message.js'></script>
    
</div>
</body>
</html>
