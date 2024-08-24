<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); 
    exit;
}

date_default_timezone_set('Asia/Manila'); 
$currentDateTime = date('l, d/m/Y h:i:s A'); 

$id = isset($_GET['id']) ? $_GET['id'] : null;
$event = [
    'title' => '',
    'description' => '',
    'start' => '',
    'end' => ''
];

if ($id) {
    // Fetch event details for editing
    $stmt = $pdo->prepare("SELECT id, title, description, event_date as start, end_date as end, event_type FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    // Redirect or handle the case where no ID is provided
    header('Location: handle_events.php');
    exit;
}

// Fetch event types from the database
try {
    $stmt = $pdo->query("SELECT type FROM event_types");
    $eventTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle the error (you can log it or display a message)
    log_error('Error fetching event types: ' . $e->getMessage(), '../../../logs/error.log');
    $eventTypes = []; // Set to an empty array in case of an error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/event_form.css">
</head>
<body>
    <div class="top-nav">
        <div class="left-section">
            <button class="sidebar-toggle-button" onclick="toggleSidebar()">☰</button>
            <div class="app-name">EduCollab</div>
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
                <a href="dashboard.php#">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div>

        <div class="content" id="content">
            <div id="datetime">
                <?php echo $currentDateTime; ?>
            </div>

            <h2>Edit Event</h2>

            <form id="eventsForm" action="../../src/processes/a/update_event.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id']); ?>">

                <div class="event-form-group">
                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="start">Start Date:</label>
                        <input type="date" class="form-control" name="start" value="<?php echo htmlspecialchars($event['start']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="end">End Date:</label>
                        <input type="date" class="form-control" name="end" value="<?php echo htmlspecialchars($event['end']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <select class="form-control" name="events[0][type]" required>
                            <option value="" disabled selected>Select an option</option>
                            <?php foreach ($eventTypes as $eventType): ?>
                                <option value="<?= htmlspecialchars($eventType['type'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($eventType['type'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Event</button>
                <button type="button" class="btn btn-secondary" onclick="openDiscardChangesModal()">Cancel</button>
            </form>
        </div>

        <?php include '../display_message.php'; ?>

        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
        <script src="../../src/js/toggleSidebar.js"></script>
        <script src="../../src/js/verify.js"></script>

        <script>
            function openDiscardChangesModal() {
                $('#discardChangesModal').modal('show');
            }

            $('#confirmDiscardButton').on('click', function() {
                window.location.href = 'handle_events.php'; // Redirect to handle_events.php
            });
        </script>
    </div>
</body>
</html>
