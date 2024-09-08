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

    <title>Dashboard</title>

</head>
<body>
    <div class="top-nav">
        <div class="left-section">
            <button class="sidebar-toggle-button" onclick="toggleSidebar()">☰</button>
            <div class="app-name">EduCollab</div>
            <!-- Current date and time -->
            <div id="datetime">
                    <?php echo $currentDateTime; ?>
            </div>
           
        </div>

       

        <!-- Bell icon with notification count -->
        <!-- <div class="notification-bell">
            <i class="bi bi-bell-fill"></i>
            <span class="notification-count">0</span>
        </div> -->
        
        <!-- Notification dropdown
        <div class="notification-dropdown">
            <ul class="notification-list"> -->
                <!-- Notifications will be appended here by JavaScript -->
            <!-- </ul>
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
        </div> -->
    </div>


    <div class="main">
        <div class="sidebar" id="sidebar">
            <div class="logo"></div> 
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div>

        <div class="content" id="content">
        
            <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>



            

            <a href="add_user.php">add new user<a>

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



        </div>

    </div>


    <!-- js scripts -->
    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
    

</body>
</html>
