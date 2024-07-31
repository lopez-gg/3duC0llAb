<?php
// my_tasks.php
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/processes/fetch_tasks.php';


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); // Redirect to login page if not logged in
    exit;
}
$userId = $_SESSION['userId']; 
// Check if the user is user
check_access('USER');

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'my_tasks';
$tasks = getTasks($userId, $filter);


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
    <title>My Tasks</title>
    <!-- Include your CSS files here -->
</head>
<body>
    <h1>My Tasks</h1>
    <div>
        <a href="?filter=my_tasks">My Tasks (Assigned by Me)</a> |
        <a href="?filter=assigned_tasks">Assigned Tasks (Assigned to Me)</a>
    </div>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <strong><?php echo htmlspecialchars($task['title']); ?></strong><br>
                <?php echo htmlspecialchars($task['description']); ?><br>
                Due Date: <?php echo htmlspecialchars($task['due_date']); ?><br>
                Urgency: <?php echo htmlspecialchars($task['tag']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
