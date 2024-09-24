<?php
// dashboard.php

require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 


$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); 
    exit;
}

$userId = ($_SESSION['user_id']);




// Set timezone
date_default_timezone_set('Asia/Manila'); 

// Get current date and time
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$currentMonth = date('F');
$currentYear = date('Y');
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/a/dashb.css">

    <title>Dashboard</title>

</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
        
            <section class='main-sec' id='sec-one'>
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            </section>

            <hr>
                
            <section class='main-sec' id='sec-two'>
                <div class="s2-e"><a href="faculty.php">Faculty</a></div>
                <div class="s2-e"><a href="general_forum.php">General Forum</a></div>
                <div class="s2-e"><a href="#">Appointment Requests</a></div>
            </section>

            <hr>

            <section class='main-sec' id='sec-three'>
                <!-- events -->
                <?php
                // Filter events to only include those in the current month
                $filteredEvents = array_filter($events, function($event) use ($currentMonth, $currentYear) {
                    $eventStartDate = new DateTime($event['event_date']);
                    return $eventStartDate->format('F') === $currentMonth && $eventStartDate->format('Y') === $currentYear;
                });
                ?>
                <div class="d-events-main-container">
                    <h1 id="events-heading"><?php echo $currentMonth; ?> Events</h1>
                    <div class="d-events-list-container">
                        <ul id="upcoming-events">
                            <?php if (!empty($filteredEvents)): ?>
                                <?php foreach ($filteredEvents as $event): ?>
                                    <a href='calendar.php'><div class=''><li>
                                        <?php
                                        $eventStartDate = new DateTime($event['event_date']);
                                        $eventEndDate = new DateTime($event['end_date']);
                                        $currentDate = new DateTime();
                                        $startDateFormatted = $eventStartDate->format('F d');

                                        if ($eventStartDate > $currentDate) {
                                            echo "<strong>{$startDateFormatted}</strong><br>";
                                            echo "Upcoming<br>";
                                            echo "{$event['title']}<br>";
                                            echo "{$event['description']}<br>";
                                        } elseif ($eventEndDate < $currentDate) {
                                            $interval = $eventEndDate->diff($currentDate);
                                            $daysPassed = $interval->format('%a');
                                            echo "<strong>{$startDateFormatted}</strong><br>";
                                            echo "{$event['title']}<br>";
                                            echo "{$daysPassed} days ago<br>";
                                        } else {
                                            echo "<strong>{$startDateFormatted}</strong><br>";
                                            echo "{$event['title']}<br>";
                                            echo "{$event['description']}<br>";
                                        }
                                        ?>
                                    </div></a></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No events for this month.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Reminders -->
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


    <!-- js scripts -->
    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
    

</body>
</html>
