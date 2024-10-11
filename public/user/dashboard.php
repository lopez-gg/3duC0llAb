<?php
// dashboard.php
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/check_due_tasks.php'; 

// $tasks = require_once __DIR__ . '/../../src/processes/u/fetch_due_tasks.php';
// var_dump($tasks);

// Check if the user is admin
check_access('USER');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php'); 
    exit;
}else{
    $ugrade = $_SESSION['ugrade'];
    $userId = $_SESSION['user_id'];
    $dashb = 'dashboard';

    if (strtolower($ugrade) === strtolower('sned')){
        $grade = 'SNED';
        $_SESSION['grade'] = $grade;
    }else{
        $grade = str_replace("Grade ", "", $ugrade);
        $_SESSION['grade'] = $grade;
    }
}



// Set timezone
date_default_timezone_set('Asia/Manila'); 

// Get current date and time
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$currentMonth = date('F');
$currentYear = date('Y');
$currentDate = new DateTime();


$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
include '../display_mod.php';
unset($_SESSION['success_message']);

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/dashb.css">
    <link rel="stylesheet" href="../../src/css/message.css">
    <link rel="stylesheet" href="../../src/css/tasks.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 

    <title>Dashboard</title>

</head>
<body>
    <?php require_once __DIR__ . '/../display_archived_task_alert.php';?>
    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
        
            <section class='main-sec' id='sec-one'>
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            </section>

            <hr>
                
            <section class='main-sec' id='sec-two'>
                <a href="faculty.php"><div class="space">Faculty</div></a>
                <!-- <a href="space_home.php"><div class="space">My <s?php echo $grade?> Tasks</div></a> -->
                <a href="my_space.php"><div class="space">My Space</div></a>
                <a href="general_forum.php"><div class="space">General Forum</div></a>
                <a href="space_forum.php?grade=<?php echo $grade?>"><div class="space"><?php echo $ugrade?> Forum</div></a>
                <a href="request_appointment.php"><div class="space">Request Appointment</div></a>
            </section>

            <hr>


            <section class='main-sec' id='sec-three'> 
                <div class="d-events-main-container">
                    <h1 id="events-heading"><?php echo date('F'); ?> Events</h1>
                    <div class="d-events-list-container">
                        <ul id="upcoming-events">
                            <?php if (!empty($events)): ?>
                                <?php foreach ($events as $event): ?>
                                    <?php
                                    $eventStartDate = new DateTime($event['event_date']);
                                    $eventEndDate = new DateTime($event['end_date']);
                                    
                                    // Determine event status based on $currentDate
                                    $status = '';
                                    if ($eventStartDate > $currentDate) {
                                        $status = 'Upcoming';
                                    } elseif ($eventEndDate < $currentDate) {
                                        $status = 'Ended';
                                    } elseif ($eventStartDate->format('Y-m-d') === $currentDate->format('Y-m-d')) {
                                        $status = 'Today';
                                    } elseif ($eventStartDate->format('Y-m-d') < $currentDate && $eventEndDate->format('Y-m-d') > $currentDate->format('Y-m-d')) {
                                        $status = 'Ongoing';
                                    }
                                    ?>
                                    <li>
                                        <div class='event-card'>
                                            <div class='event-info'>
                                                <div class='event-date'>
                                                    <?php echo $eventStartDate->format('d'); // Display the event day ?>
                                                </div>
                                                <div class='event-details'>
                                                    <strong><?php echo $event['title']; ?></strong><br>
                                                    <p class='event-status'><?php echo $status; ?></p>
                                                    <p class='event-description'>
                                                        <?php echo substr($event['description'], 0, 50) . (strlen($event['description']) > 50 ? '...' : ''); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No events for this month.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>


                <div class="reminders-con">
                    <h1 class="reminders-heading">Reminders</h1>
                    <div class="reminders-lists">
                        
                    </div>
                </div>
            </section>



            <hr>
            <section class='main-sec' id='sec-three'> 
                <div class="due-tasks-con">
                    <div class="due-task-head">
                        <h1 class="due-tasks-con-heading">Due Tasks</h1>
                        <button class="btn legendBtn" id="legend" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Legend">
                            <i class="bi bi-patch-question"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="legend">
                        <ul class="legend-list-group">
                            <li class="legend-list-group-item">
                                <span class="badge" style="background-color: white; border: 1px solid gray;">&nbsp;&nbsp;</span> - Normal
                            </li>
                            <li class="legend-list-group-item">
                                <span class="badge" style="background-color: yellow;">&nbsp;&nbsp;</span> - Urgent
                            </li>
                            <li class="legend-list-group-item">
                                <span class="badge" style="background-color: orange;">&nbsp;&nbsp;</span> - Important
                            </li>
                            <li class="legend-list-group-item">
                                <span class="badge" style="background-color: red;">&nbsp;&nbsp;</span> - Urgent and Important
                            </li>  
                            </ul>
                        </div>
                    </div>
                    
                    <div class="dashb-task-list-container">
                        <?php require_once __DIR__ . '/../../src/processes/u/fetch_due_tasks.php';?>
                    </div>
                </div>
            </section>

        </div>

    </div>
    
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
    <script src='../../src/js/message.js'></script>
    <script src='../../src/js/reminder.js'></script>

    <script>
          $(document).ready(function () {
            $('.task-data-select').on('change', function () {
                const taskId = $(this).data('task-id');
                const newProgress = $(this).val();
                const csrfToken = '<?= $_SESSION['csrf_token'] ?>'; // Assuming CSRF token is set in session

                $.ajax({
                    url: '../../src/processes/update_task_progress.php',
                    type: 'POST',
                    data: {
                        id: taskId,
                        progress: newProgress,
                        csrf_token: csrfToken // Include the CSRF token
                    },
                    success: function (response) {
                        let jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            $('#successModal .modal-body').text(jsonResponse.message);
                    
                            $('#successModal').modal('show');
                            setTimeout(function() {
                                $('#successModal').modal('hide');
                            }, 4500);
                        }
                    },
                    error: function (xhr, status, error) {
                        let jsonResponse = JSON.parse(response);
                        if (jsonResponse.error) {
                            $('#successModal .modal-body').text(jsonResponse.message);
                    
                            $('#successModal').modal('show');
                            setTimeout(function() {
                                $('#successModal').modal('hide');
                            }, 4500);
                        }
                    }
                });
            });
        });
    </script>
    


    </script>
</div>
</body>
</html>
