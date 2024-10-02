<!-- add_new_task.php -->
<?php
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php'; 
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/check_new_messages.php'; 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}else{
    $grade = (string)$_SESSION['grade'];
    if ($grade === 'sned') {
        $gradetodisplay = 'SNED'; 
    } else {
        $gradetodisplay = 'Grade ' . (string)$_SESSION['grade']; 
    }
}

// Check if the user is admin
check_access('ADMIN');

// Set timezone
date_default_timezone_set('Asia/Manila'); 

// Get current date and time
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$currentMonth = date('F');
$currentYear = date('Y');


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
    <title>Assign Task</title>
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> -->
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/form.css">
    <link rel="stylesheet" href="../../src/css/message.css">

</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>
        
        <div class="content" id="content">
            <h2><?php echo $gradetodisplay?> > Assign Task</h2>
        
            <div class="form-container"> 
                <form action="../../src/processes/a/process_assign_task.php" method="POST">
                    <!-- Task Title -->
                    <div class="form-group">
                        <label for="title">Task Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <!-- Assigned To Search -->
                    <div class="form-group position-relative">
                        <label for="assignedToSearch">Assign To </label>
                        <input type="text" class="form-control" id="assignedToSearch" autocomplete="off" placeholder="Search here...">
                        <div id="searchResults" class="search-results"></div>
                        <input type="hidden" id="assignedTo" name="assignedTo">
                    </div>

                    <!-- Task Description -->
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <!-- Urgency Selection -->
                    <div class="t-urgency-container">
                        <label class='t-urgency-e'>
                            <input type='radio' name='urgency' value='Normal' default/> Normal
                        </label>
                        <label class='t-urgency-e'>
                            <input type='radio' name='urgency' value='Urgent'/> Urgent
                        </label>   
                        <label class='t-urgency-e'>
                            <input type='radio' name='urgency' value='Important'/> Important
                        </label>
                        <label class='t-urgency-e'>
                            <input type='radio' name='urgency' value='Urgent and Important'/> Urgent and Important
                        </label>
                    </div>

                    <!-- Due Date -->
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="date" class="form-control" id="due_date" name="due_date">
                    </div>
                    <div class="form-group">
                        <label for="due_time">Due Time</label>
                        <input type="time" class="form-control" id="due_time" name="due_time" value="<?php echo htmlspecialchars($task['due_time']); ?>">
                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" name="taskType" value="assigned">
                    <input type="hidden" name="assignedBy" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="grade" value="<?php echo $grade?>">

                    <!-- Submit and Cancel Buttons -->
                    <button type="submit" class="btn btn-primary">Add Task</button>
                    <button type="button" class="btn btn-danger" 
                            onclick="openVerificationModal('cancel_form_', 'Cancel', 'All entries will be discarded. Are you sure you want to cancel?', 'Yes', 'space_home.php?grade=<?=$grade?>', '1')">Cancel
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../src/js/toggleSidebar.js"></script>
    <script src="../../src/js/verify.js"></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/message.js'></script>
    
    <script>
        $(document).ready(function() {
            $('#assignedToSearch').on('keyup', function() {
                let query = $(this).val();
                let grade = '<?= $grade ?>';  // Ensure this outputs the correct grade value

                if (query.length > 1) {
                    $.ajax({
                        url: '../../src/processes/a/search_users_by_grade.php',
                        method: 'POST',
                        data: { query: query, grade: grade },
                        success: function(data) {
                            $('#searchResults').html(data);
                        },
                        error: function(xhr, status, error) {
                            console.log("Error:", error);  
                            $('#searchResults').html('<div class="search-result-item">Error occurred. Please try again.</div>');
                        }
                    });
                } else if (query.length === 0) {
                    $('#searchResults').html('');  
                } 
            });

            // When a search result is clicked, populate the hidden input with user ID
            $(document).on('click', '.search-result-item', function() {
                let userId = $(this).data('userid');
                let userInfo = $(this).text();
                
                $('#assignedToSearch').val(userInfo);  // Set the visible field
                $('#assignedTo').val(userId);  // Set the hidden field with user ID
                $('#searchResults').html('');  // Clear the search results
            });
        });
    </script>





</body>
</html>

