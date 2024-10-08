<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php';


$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 


// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}
$calendr = 'calendar';


include '../display_mod.php';
date_default_timezone_set('Asia/Manila'); 
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$sy = $_GET['sy'] ?? null;


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
    <title>Add Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/message.css">
    <link rel="stylesheet" href="../../src/css/form.css">

</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>
        <div class="content" id="content">

            <h3>SY <?php echo $sy?></h3>
            <h2>Calendar > Add Event</h2>

            <form id="eventsForm" action="../../src/processes/a/add_event.php" method="POST">
                <div class="form-container" id="form-container"> <!-- Apply the form-container class from your CSS -->
                    <div class="event-form-group">
                        <input type="hidden" name="events[0][year_range]" value="<?= htmlspecialchars($sy, ENT_QUOTES, 'UTF-8') ?>">
                        
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" name="events[0][title]" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea class="form-control" name="events[0][description]" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="start">Start Date:</label>
                            <input type="date" class="form-control" name="events[0][start]" required>
                        </div>

                        <div class="form-group">
                            <label for="end">End Date:</label>
                            <input type="date" class="form-control" name="events[0][end]" required>
                        </div>

                        <div class="form-group">
                            <label for="type">Type:</label>
                            <select class="form-control" name="events[0][type]" required>
                                <option value="" disabled selected>Set event type</option>
                                <?php foreach ($eventTypes as $eventType): ?>
                                    <option value="<?= htmlspecialchars($eventType['type'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($eventType['type'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary" id="addNewEvent">Add New Event</button>
                <button type="submit" class="btn btn-primary">Save Events</button>
                <button type="button" class="btn btn-danger" onclick="openVerificationModal('cancel_form_', 'Cancel', 'All entries will be discarded. Are you sure you want to cancel?', 'Yes', 'manage_events.php', '1')">Cancel</button>
            </form>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script src="../../src/js/toggleSidebar.js"></script>
        <script src="../../src/js/verify.js"></script>
        <script src='../../src/js/notification.js'></script>
        <script src='../../src/js/datetime.js'></script>
        <script src='../../src/js/message.js'></script>

        <script>
            $(document).ready(function() {
                var eventCount = 1;

                $('#addNewEvent').click(function() {
                    var newForm = `
                        <div class="event-form-group">
                            <input type="hidden" name="events[${eventCount}][year_range]" value="<?= htmlspecialchars($sy, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" name="events[${eventCount}][title]" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" name="events[${eventCount}][description]" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="start">Start Date:</label>
                                <input type="date" class="form-control" name="events[${eventCount}][start]" required>
                            </div>
                            <div class="form-group">
                                <label for="end">End Date:</label>
                                <input type="date" class="form-control" name="events[${eventCount}][end]" required>
                            </div>
                            <div class="form-group">
                                <label for="type">Type:</label>
                                <select class="form-control" name="events[${eventCount}][type]" required>
                                    <option value="" disabled selected>Set event type</option>
                                    <?php foreach ($eventTypes as $eventType): ?>
                                        <option value="<?= htmlspecialchars($eventType['type'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($eventType['type'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="button" class="btn btn-danger remove-event-btn">Remove</button>
                            <hr>
                        </div>
                    `;
                    $('#form-container').append(newForm);
                    eventCount++;
                });

                // Event delegation for remove buttons
                $('#form-container').on('click', '.remove-event-btn', function() {
                    $(this).closest('.event-form-group').remove();
                });
            });
        </script>

        </script>
    </div>
</body>
</html>
