<?php
// dashboard.php

require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';


$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); 
    exit;
}

$userId = ($_SESSION['user_id']);

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Destroy the session
    session_unset();
    session_destroy();
    
    // Redirect to the login page
    header('Location: ../login.php');
    exit;
}



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
    <div class="sidebar">
        <div class="logo"></div> <!-- Logo box -->
        <div class="nav-links">
            <a class='active' href="dashboard.php">Dashboard</a>
            <a href="calendar.php">Calendar</a>
        </div>
    </div>

    <div class="content">
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <!-- Logout Form -->
        <form action="" method="post">
            <input type="submit" name="logout" value="Logout">
        </form>

        <!-- Current date and time -->
        <div id="datetime">
            <?php echo $currentDateTime; ?>
        </div>
        <!-- events -->
        <?php
        // Filter events to only include those in the current month
        $filteredEvents = array_filter($events, function($event) use ($currentMonth, $currentYear) {
            $eventStartDate = new DateTime($event['event_date']);
            return $eventStartDate->format('F') === $currentMonth && $eventStartDate->format('Y') === $currentYear;
        });
        ?>

        <h1 id="events-heading"><?php echo $currentMonth; ?> Events</h1>
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


        <a href="add_user.php">add new user<a>
        </div>
    <!-- js scripts -->
    <script src='../../src/js/datetime.js'></script>
</body>
</html>
