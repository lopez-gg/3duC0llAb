<!-- add_new_task.php -->
<?php
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php'; 
require_once __DIR__ . '/../../src/config/db_config.php'; 

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
        <div class="sidebar" id="sidebar">
                <div class="logo"></div> 
                <div class="nav-links">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="calendar.php">Calendar</a>
                </div>
            </div>

        
        <div class="content" id="content">
            <!-- <div class="container mt-5"> -->
                <h2><?php echo $gradetodisplay?> > Assign Task</h2>
                
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

                    <!-- Urgency Selection (use radio buttons instead of checkboxes) -->
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

                    <!-- Auto-set Task Type and Assigned By (hidden fields) -->
                    <input type="hidden" name="taskType" value="assigned">
                    <input type="hidden" name="assignedBy" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="grade" value="<?php echo $grade?>">

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">Add Task</button>
                    <button type="button" class="btn btn-danger" onclick="openVerificationModal('cancel_form_', 'Cancel', 'All entries will be discarded. Are you sure you want to cancel?  ', 'Yes', 'space_home.php?grade=<?=$grade?>', '1')">Cancel</button>
                </form>
            <!-- </div> -->
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../src/js/toggleSidebar.js"></script>
    <script src="../../src/js/verify.js"></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/datetime.js'></script>
    
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

