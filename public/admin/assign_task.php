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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .search-results {
            border: 1px solid #ddd;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            z-index: 1000;
            background: #fff;
            width: 100%;
        }
        .search-result-item {
            padding: 10px;
            cursor: pointer;
        }
        .search-result-item:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Assign Task</h2>
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

            <!-- Due Date -->
            <div class="form-group">
                <label for="due_date">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date">
            </div>

            <!-- Auto-set Task Type and Assigned By (hidden fields) -->
            <input type="hidden" name="taskType" value="assigned">
            <input type="hidden" name="assignedBy" value="<?php echo $_SESSION['user_id']; ?>">

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Add Task</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#assignedToSearch').on('keyup', function() {
                let query = $(this).val();

                if (query.length > 2) {

                    $.ajax({
                        url: '../../src/processes/search_users.php',
                        method: 'POST',
                        data: { query: query },
                        success: function(data) {
                            $('#searchResults').html(data); // Display the search results
                        },
                        error: function(xhr, status, error) {
                            console.log("Error:", error);  // Log errors if any
                            $('#searchResults').html('<div class="search-result-item">Error occurred. Please try again.</div>');
                        }
                    });
                } else {
                    $('#searchResults').html('<div class="search-result-item">Searching...</div>');  // Clear results if fewer than 3 characters are typed
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

