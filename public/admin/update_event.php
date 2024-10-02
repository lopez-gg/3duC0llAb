<?php

require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/check_new_messages.php'; 

$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 
include '../../src/processes/fetch_sy.php';

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); 
    exit;
}

$dashb = '';
$my_space = '';
$calendr = 'calendar';
$gen_forum ='';

date_default_timezone_set('Asia/Manila'); 
$currentDateTime = date('l, d/m/Y h:i:s A'); 

$id = isset($_GET['id']) ? $_GET['id'] : null;
$event = [
    'title' => '',
    'description' => '',
    'start' => '',
    'end' => '',
    'type' => '',
    'year_range' => ''
];

if ($id) {
    // Fetch event details for editing
    $stmt = $pdo->prepare("SELECT id, title, description, event_date as start, end_date as end, event_type as type, year_range FROM events WHERE id = ?");
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
    $eventTypes = []; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/form.css">
    <link rel="stylesheet" href="../../src/css/message.css">
</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>
    <div class="content" id="content">
        <h2>Calendar > Edit Event</h2>

        <div class="form-container">
            <form id="eventsForm" action="../../src/processes/a/update_event.php" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id']); ?>">

                <div class="event-form-group">
                    <div class="form-group">
                        <label for="year_range">School Year:</label>
                        <select class="form-control" name="year_range" required>
                            <option value="<?= htmlspecialchars($event['year_range'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                <?= htmlspecialchars($event['year_range'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php foreach ($yearRanges as $range): ?>
                                <option value="<?= htmlspecialchars($range['year_range'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($range['year_range'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

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
                        <select class="form-control" name="type" required>
                            <option value="<?= htmlspecialchars($event['type'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                <?= htmlspecialchars($event['type'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
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
    </div>


        <?php include '../display_mod.php'; ?>

        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

        <script src="../../src/js/toggleSidebar.js"></script>
        <script src="../../src/js/verify.js"></script>
        <script src='../../src/js/notification.js'></script>
        <script src='../../src/js/datetime.js'></script>
        <script src='../../src/js/message.js'></script>
    </div>
    
    <script>
        function openDiscardChangesModal() {
            $('#discardChangesModal').modal('show');
        }

        $('#confirmDiscardButton').on('click', function() {
            window.location.href = 'manage_events.php'; 
        });
    </script>
    
</body>
</html>
