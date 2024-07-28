<?php
// dashboard.php

require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';

// Check if the user is user
check_access('USER');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); // Redirect to login page if not logged in
    exit;
}

// Handle logout request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Destroy the session
    session_unset();
    session_destroy();
    
    // Redirect to the login page
    header('Location: ../login.php');
    exit;
}

// Fetch events
$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; // Include fetch_events.php only once

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

    <title>Dashboard</title>

    <style>
        #upcoming-events {
            list-style-type: none;
        }
        #upcoming-events li {
            margin: 10px 0;
        }
    </style>
</head>
<body>
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
    <h1 id="events-heading"><?php echo $currentMonth; ?> Events</h1>
    <ul id="upcoming-events">
        <?php if (!empty($events)): ?>
            <?php foreach ($events as $event): ?>
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

    
    <!-- js scripts -->
    <script src='../../src/js/datetime.js'></script>
</body>
</html>
