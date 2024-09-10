<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 


// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// fetching tasks per grade
$grade = $_GET['grade'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['base_url'] . "/src/processes/a/fetch_tasks.php?grade=" . urlencode($grade));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$tasks_json = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$tasks = json_decode($tasks_json, true);
if (isset($tasks['error'])) {
    echo "<p>Error: " . $tasks['error'] . "</p>";
}


// Set default values for the variables
$currentDateTime = date('l, d/m/Y h:i:s A'); 



?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Grade <?php echo $grade ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        
        <link href="../../src/css/gen.css" rel="stylesheet">
    </head>
    <body>
        <!-- top navigation -->
        <!--<div class="top-nav">
            <div class="left-section">
                <button class="sidebar-toggle-button" onclick="toggleSidebar()">☰</button>
                <div class="app-name">EduCollab</div>
                <div id="datetime"> -->
                    <?php echo $currentDateTime; ?>
                <!-- </div>
            </div>

            <div class="right-section"> -->
                <!-- Bell icon with notification count -->
                <div class="notification-bell">
                    <i class="bi bi-bell-fill"></i>
                    <span class="notification-count">0</span>
                </div>
                
                <!-- Notification dropdown-->
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
        </div> 

        <!-- sidebar -->
        <div class="main">
            <!-- <div class="sidebar" id="sidebar">
                    <div class="logo"></div> 
                    <div class="nav-links">
                        <a href="dashboard.php">Dashboard</a>
                        <a href="calendar.php">Calendar</a>
                    </div>
                </div> -->

                <!-- date and time -->
                <div class="content" id="content">

                    <section class='main-sec' id='sec-one'>
                        <h2>Grade <?php echo $grade?></h2>
                    </section>

                    <section class="main-sec" id="sec-two">
                        <div class="s2-e">
                            <a href="">Announcements</a>
                            <a href="">Grade <?php echo $grade?> Faculty</a>
                        </div>
                    </section>

                    <section class="main-sec" id="sec-three">
                    <?php


                // Display the tasks
                if (!empty($tasks)) {
                    echo "<h2 class='notFoundMsg'>No tasks found for : $grade</h2>";
                    echo "<ul>";
                    foreach ($tasks as $task) {
                        echo "<li>";
                        echo "<div class='task-tag' style='color:" . htmlspecialchars($task['tag']) . "' title='" . htmlspecialchars($tag) . "'></div><br>";
                        echo "<strong>Description:</strong> " . htmlspecialchars($task['description']) . "<br>";
                        echo "<strong>Status:</strong> " . htmlspecialchars($task['status']) . "<br>";
                        echo "<strong>Description:</strong> " . htmlspecialchars($task['description']) . "<br>";
                        echo "<strong>Due Date:</strong> " . htmlspecialchars($task['due_date']) . "<br>";
                        echo "<strong>Created At:</strong> " . htmlspecialchars($task['created_at']) . "<br>";
                        echo "<strong>Description:</strong> " . htmlspecialchars($task['description']) . "<br>";
                        echo "</li><br>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No tasks found for this grade.</p>";
                }
            ?>


                        </section>
                        




                    
                        
        </div>

    </body>
</html>