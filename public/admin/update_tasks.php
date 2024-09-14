<?php

require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';

check_access('ADMIN');

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); 
    exit;
}else {
    $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
    $_SESSION['grade'] = $grade;

    if ($grade === 'sned'){
        $gradetodisplay = strtoupper($grade);
    } else {
        $gradetodisplay = 'Grade ' . $grade;
    }
}

date_default_timezone_set('Asia/Manila'); 
$currentDateTime = date('l, d/m/Y h:i:s A'); 

$grade = isset($_GET['grade']) ? $_GET['grade'] : null;
$id = isset($_GET['id']) ? $_GET['id'] : null;
$task = [
    'title' => '',
    'description' => '',
    'tag' => '',
    'grade' => '',
    'progress' => '',
    'due_date' => '',
    'due_time' => ''
];

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header('Location: manage_tasks.php');
    exit;
}

$tags = ['Normal', 'Urgent', 'Important'];
$progressStatuses = ['completed', 'in_progress', 'pending'];

// debug
// if ($id && $grade) {
//     echo "Task ID: " . htmlspecialchars($id) . "<br>";
//     echo "Grade: " . htmlspecialchars($grade) . "<br>";
// } else {
//     echo "Missing task ID or grade.";
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/task_form.css">
</head>
<body>
        <!-- <div class="top-nav"> -->
            <div class="left-section">
                <button class="sidebar-toggle-button" onclick="toggleSidebar()">☰</button>
                <div class="app-name">EduCollab</div>
                <div id="datetime">
                    <?php echo $currentDateTime; ?>
                </div>
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
        <!-- <div class="sidebar" id="sidebar">
            <div class="logo"></div> 
            <div class="nav-links">
                <a href="dashboard.php#">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div> -->

        <div class="content" id="content">

            <h2><?php echo $gradetodisplay?> > Edit Task</h2>


            <form id="taskForm" action="../../src/processes/a/process_update_task.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id']); ?>">

                <div class="task-form-group">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" name="description"><?php echo htmlspecialchars($task['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="taskType">Task Type:</label>
                        <select class="form-control" name="taskType" required>
                            <option value="<?= htmlspecialchars($task['taskType'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                <?= htmlspecialchars($task['taskType'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tag">Tag:</label>
                        <select class="form-control" name="tag" required>
                            <option value="<?= htmlspecialchars($task['tag'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                <?= htmlspecialchars($task['tag'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php foreach ($tags as $tag): ?>
                                <option value="<?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="grade">Grade:</label>
                        <input type="text" class="form-control" name="grade" value="<?php echo htmlspecialchars($task['grade']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="progress">Progress:</label>
                        <select class="form-control" name="progress" required>
                            <option value="<?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                <?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php foreach ($progressStatuses as $status): ?>
                                <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Due Date:</label>
                        <input type="date" class="form-control" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="due_time">Due Time:</label>
                        <input type="time" class="form-control" name="due_time" value="<?php echo htmlspecialchars($task['due_time']); ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Task</button>
                <button type="button" class="btn btn-secondary" onclick="openVerificationModal('discard_changes_<?php echo htmlspecialchars($task['id'] ?? ''); ?>', 'Confirm Deletion', 'Are you sure you want to discard changes?', 'Discard', 'space_home.php?grade=<?= $grade?>', '1')">Cancel</button>
            </form>
        </div>

    

        <?php include '../display_mod.php'; ?>

        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

        <script src="../../src/js/toggleSidebar.js"></script>
        <script src="../../src/js/verify.js"></script>
        <script src='../../src/js/notification.js'></script>
        <script src='../../src/js/datetime.js'></script>

        <script>
            function openDiscardChangesModal() {
                $('#discardChangesModal').modal('show');
            }

            $('#confirmDiscardButton').on('click', function() {
                window.location.href = 'manage_events.php'; 
            });
        </script>
    </div>
   
</body>
</html>
