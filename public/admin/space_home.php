<?php

require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php'; // Include session config
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
} else {
    $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
    $_SESSION['grade'] = $grade;

    if ($grade === 'sned'){
        $gradetodisplay = strtoupper($grade);
    } else {
        $gradetodisplay = 'Grade ' . $grade;
    }
}

// Set default values for the variables
$currentDateTime = date('l, d/m/Y h:i:s A'); 
// Handle Pagination Variables
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Initialize $currentPage
$itemsPerPage = $data['itemsPerPage'] ?? 10; 
$index = ($currentPage - 1) * $itemsPerPage + 1; 
$params = [
    'grade' => $grade, // Grade is mandatory for displaying tasks
    'order' => isset($_GET['order']) ? $_GET['order'] : 'desc', // Sort order (optional)
    'progress' => isset($_GET['progress']) ? $_GET['progress'] : '', // Task progress (optional)
    'page' => isset($_GET['page']) ? $_GET['page'] : 1 // Pagination (optional)
];

// Build the query string
$queryString = http_build_query($params);

// Fetching tasks per grade
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['base_url'] . "/src/processes/a/fetch_space_tasks.php?" . $queryString);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$tasks_json = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$tasks = json_decode($tasks_json, true);

if (isset($tasks['error'])) {
    echo "<p>Error: " . htmlspecialchars($tasks['error']) . "</p>";
}

$totalPages = $tasks['totalPages'] ?? 1; // Default to 1 if not set

$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
include '../display_mod.php';
unset($_SESSION['success_message']);

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($gradetodisplay); ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../src/css/gen.css" rel="stylesheet">
        <link href="../../src/css/a/dashb.css" rel="stylesheet">
    </head>
    <body>
        <!-- top navigation -->
        <!-- <div class="top-nav"> -->
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
        <!-- </div>  -->

        <!-- sidebar -->
        <div class="main">
            <!-- <div class="sidebar" id="sidebar">
                <div class="logo"></div> 
                <div class="nav-links">
                    <a href="dashboard.php">Dashboard</a>
                    <a href="calendar.php">Calendar</a>
                </div>
            </div> -->

            <!-- date and time -->
            <div class="content" id="content">
                <section class='main-sec' id='sec-one'>
                    <h2> <?php echo strtoupper(htmlspecialchars($gradetodisplay)); ?></h2>
                </section>

                <section class="main-sec" id="sec-two">
                    <div class="s2-e">
                        <a href="assign_task.php">Assign Task</a>
                    </div>
                    <div class="s2-e">
                        <a href="">Announcements</a>
                    </div>
                    <div class="s2-e">
                        <a href=""> <?php echo htmlspecialchars($grade); ?> Faculty</a>
                    </div>
                </section>

                <hr>
                <section>
                    <div class="">
                        <button class="btn btn-secondary dropdown-toggle" id="sortIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Sort order">
                            <i class="bi bi-funnel"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="sortIcon">
                            <a class="dropdown-item sort-option" data-order="asc" >Ascending</a>
                            <a class="dropdown-item sort-option" data-order="desc" >Descending</a>
                        </div>
                    </div>
                </section>
                <section class="main-sec" id="sec-three">
                    

                    <div class="task-list-container">
                        <?php if (empty($tasks['tasks'])): ?>
                            <div>No tasks found.</div>
                        <?php else: 
                            ?>
                            <?php foreach ($tasks['tasks'] as $task): ?>
                                <div class="task-card" style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; border-radius: 10px;">
                                    <div class="task-header" style="display: flex; justify-content: space-between; align-items: center;">
                                        <h5 style="margin: 0;"><?php echo htmlspecialchars($task['title'] ?? 'Untitled Task'); ?></h5>
                                        <div class="task-status">
                                            <?php $color = isset($task['tag']) ? getUrgencyColor($task['tag']) : 'gray'; ?>
                                            <div style="height: 20px; width: 20px; background-color: <?php echo htmlspecialchars($color); ?>; border-radius: 50%;" title="<?php echo htmlspecialchars($task['tag'] ?? ''); ?>"></div>
                                        </div>
                                    </div>
                                    <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($task['assigned_username'] ?? 'Unassigned'); ?></p>
                                    <p><strong>Due Date:</strong> <?php echo htmlspecialchars($task['due_date'] ?? 'No Due Date'); ?></p>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($task['progress'] ?? 'Unknown'); ?></p>
                                    <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description'] ?? 'No description'); ?></p>
                                    <div class="task-actions" style="display: flex; gap: 10px;">
                                        <form action="update_tasks.php" method="GET" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id'] ?? ''); ?>">
                                            <input type="hidden" name="grade" value="<?= htmlspecialchars($grade); ?>">
                                            <button type="submit" class="btn btn-normal" title="Edit Task">
                                                <i class="bi bi-pencil-square"></i> 
                                            </button>
                                        </form>
                                        <form id="deleteForm_<?php echo htmlspecialchars($task['id'] ?? ''); ?>" action="../../src/processes/a/delete_task.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($task['id'] ?? ''); ?>">
                                            <input type="hidden" name="grade" value="<?= htmlspecialchars($grade); ?>">
                                        </form>
                                        <button type="button" class="btn btn-danger" title="Delete Task" onclick="openVerificationModal('deleteForm_<?php echo htmlspecialchars($task['id'] ?? ''); ?>', 'Confirm Deletion', 'Are you sure you want to delete this task?', 'Delete', 'space_home.php?grade=<?= $grade?>', '1')">
                                            <i class="bi bi-trash3"></i> 
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <nav aria-label="Task pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php if ($currentPage <= 1) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&grade=<?php echo urlencode($grade); ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                <li class="page-item <?php if ($page == $currentPage) echo 'active'; ?>">
                                    <a class="page-link" href="?page=<?php echo $page; ?>&grade=<?php echo urlencode($grade); ?>"><?php echo $page; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&grade=<?php echo urlencode($grade); ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>


                </section>

            </div>
        </div>

        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

        
        <script src='../../src/js/datetime.js'></script>
        <!-- <script src="../../src/js/toggleSidebar.js"></script> -->
        <script src="../../src/js/verify.js"></script>
        <script src="../../src/js/new_sy.js"></script>
        <script src='../../src/js/notification.js'></script>


        <script>
            $(window).on('load', function() {
                <?php if ($successMessage): ?>
                    $('#successModal').modal('show');
                    setTimeout(function() {
                        $('#successModal').modal('hide');
                    }, 4500);
                <?php endif; ?>
            });

            $(document).ready(function () {
                // Event listener for sorting options
                $('.sort-option').on('click', function () {
                    const order = $(this).data('order'); // Fetch sort order from button click
                    
                    // Collect existing URL parameters
                    const params = {
                        grade: "<?php echo urlencode($grade); ?>", // Grade is always essential
                        order: order, // New sort order
                        progress: "<?php echo isset($_GET['progress']) ? urlencode($_GET['progress']) : ''; ?>", // Keep current filters
                        page: "<?php echo $currentPage; ?>" // Current page
                    };

                    // Build the query string
                    const queryString = $.param(params);

                    // Reload page with updated query
                    window.location.href = `space_home.php?${queryString}`;
                });
            });
</script>


        </script>

    </body>
</html>
