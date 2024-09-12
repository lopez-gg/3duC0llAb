<?php

require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php'; // Include session config
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
} else {
    $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
    $_SESSION['grade'] = $grade;
}

// Fetching tasks per grade
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
    echo "<p>Error: " . htmlspecialchars($tasks['error']) . "</p>";
}

// Set default values for the variables
$currentDateTime = date('l, d/m/Y h:i:s A'); 

$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
include '../display_mod.php';
unset($_SESSION['success_message']);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Grade <?php echo htmlspecialchars($grade); ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../src/css/gen.css" rel="stylesheet">
        <link href="../../src/css/a/dashb.css" rel="stylesheet">
    </head>
    <body>
        <!-- top navigation -->
        <!-- Adjusted to use dynamic content and removed unused sections -->
        <div class="top-nav">
            <div class="left-section">
                <button class="sidebar-toggle-button" onclick="toggleSidebar()">☰</button>
                <div class="app-name">EduCollab</div>
                <div id="datetime"><?php echo htmlspecialchars($currentDateTime); ?></div>
            </div>

            <div class="right-section">
                <div class="notification-bell">
                    <i class="bi bi-bell-fill"></i>
                    <span class="notification-count">0</span>
                </div>
                
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
                    <h2>Grade <?php echo htmlspecialchars($grade); ?></h2>
                </section>

                <section class="main-sec" id="sec-two">
                    <div class="s2-e">
                        <a href="assign_task.php">Assign Task</a>
                    </div>
                    <div class="s2-e">
                        <a href="">Announcements</a>
                    </div>
                    <div class="s2-e">
                        <a href="">Grade <?php echo htmlspecialchars($grade); ?> Faculty</a>
                    </div>
                </section>

                <section class="main-sec" id="sec-three">
                    <?php
                    // Display the tasks
                    if (!empty($tasks)) {
                        echo "<ul>";
                        foreach ($tasks as $task) {
                            $color = getUrgencyColor($task['tag']);
                            echo "<div class='task-container'>";  // Debugging background
                            echo "<li>";
                            
                            // Set background-color instead of color for the circle
                            echo "<div class='task-tag' style='height: 15px; width: 15px; border-radius: 50%; background-color:" . htmlspecialchars($color) . ";' title='" . htmlspecialchars($task['tag']) . "'></div><br>";
                            
                            echo "<strong>Task Name:</strong> " . htmlspecialchars($task['title']) . "<br>";
                            echo "<strong>Assigned To:</strong> " . htmlspecialchars($task['assigned_username']) . "<br>";
                            echo "<strong>Due Date:</strong> " . htmlspecialchars($task['due_date']) . "<br>";
                            echo "<strong>Created At:</strong> " . htmlspecialchars($task['created_at']) . "<br>";
                            echo "<strong>Status:</strong> " . htmlspecialchars($task['status']) . "<br>";
                            echo "<strong>Description:</strong> " . htmlspecialchars($task['description']) . "<br>";
                            
                            echo "</li>";
                            echo "</div><br>"; 
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No tasks found for this grade.</p>";
                    }
                    ?>
                </section>
            </div>
        </div>

        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
        <!-- <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script> -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

        <script src="../../src/js/toggleSidebar.js"></script>
        <script src="../../src/js/verify.js"></script>
        <script src="../../src/js/yr_select.js"></script>
        <script src="../../src/js/new_sy.js"></script>
        <script src='../../src/js/notification.js'></script>


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
