<!-- /views/add_new_task.php -->
<?php
require_once __DIR__ . '/../../src/config/session_config.php'; // Include session config
require_once __DIR__ . '/../../src/config/config.php'; // Include general config
require_once __DIR__ . '/../../src/config/db_config.php'; // Include database config

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
    <title>Add New Task</title>
    <!-- Add any additional CSS files here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../src/css/gen.css">
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
                <a href="dashboard.php">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div>

        <div class="content" id="content">

            <!-- Current date and time -->
            <div id="datetime">
                <?php echo $currentDateTime; ?>
            </div>

            <div class="container mt-5">
                <h2>Add New Task</h2>
                <form action="../../src/processes/process_add_task.php" method="POST">
                    <div class="form-group">
                        <label for="title">Task Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="assignedTo">Assigned To</label>
                        <input type="number" class="form-control" id="assignedTo" name="assignedTo" required>
                    </div>
                    <div class="form-group">
                        <label for="tag">Urgency</label>
                        <select class="form-control" id="tag" name="tag">
                            <option value="UI">Urgent and important</option>
                            <option value="UNI">Urgent but not important</option>
                            <option value="NUI">Not Urgent but important</option>
                            <option value="NUNI">Not Urgent and Not important</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Add any additional JS files here -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script> src='../../src/js/datetime.js'</script>
    <script src="../../src/js/toggleSidebar.js"></script>
    <script src='../../src/js/notification.js'></script>
</body>
</html>
