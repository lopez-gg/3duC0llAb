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

// Fetch events
$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 
//Fetch top tasks
require_once __DIR__ . '/../../src/processes/fetch_tasks.php'; 
$topTasks = getTopTasks($userId);


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
    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

    <!-- Logout Form -->
    <form action="" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

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
    </div>


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
    
    <h2>Upcoming due tasks</h2>
    <ul>
        <?php foreach ($topTasks as $task): ?>
            <li>
                <strong><?php echo htmlspecialchars($task['title']); ?></strong><br>
                <?php echo htmlspecialchars($task['description']); ?><br>
                Due Date: <?php echo htmlspecialchars($task['due_date']); ?><br>
                Urgency: 
                <span class="urgency-circle" data-tooltip="<?php echo htmlspecialchars(getUrgencyLabel($task['tag'])); ?>" style="background-color: <?php echo getUrgencyColor($task['tag']); ?>;"></span>
            </li>
        <?php endforeach; ?>
    </ul>


    
    <!-- js scripts -->
    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/notification.js'></script>
</body>
</html>
