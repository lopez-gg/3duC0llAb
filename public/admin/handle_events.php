<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';

include '../../src/processes/fetch_sy.php';

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// Set default values for the variables
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$month = isset($_GET['month']) ? (int)$_GET['month'] : null;
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$yearRange = isset($_GET['year_range']) ? $_GET['year_range'] : getCurrentYearRange();


// Generate the base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$baseURL = $protocol . $host . '/EduCollab/src/processes/a/fetch_manage_events.php';

// Capture current parameters
$params = [
    'page' => $currentPage,
    'order' => $order,
    'month' => $month,
    'year_range' => $yearRange
];

// Build URL with current parameters
$url = $baseURL . '?' . http_build_query(array_filter($params));

// Debugging: Print URL to ensure it's built correctly
// echo "Constructed URL: " . htmlspecialchars($url) . "<br>";

// Fetch paginated events
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);

if ($response === false) {
    $error = curl_error($ch);
    curl_close($ch);
    die('Error fetching events: ' . $error);
}

curl_close($ch);

//Output the raw JSON response for debugging
// header('Content-Type: application/json');
// echo $response; // Check what this outputs

$data = json_decode($response, true);
if ($data === null) {
    die('Error decoding JSON: ' . json_last_error_msg());
}

// default sy
$currentYear = date('Y');
$nextYear = $currentYear + 1;
$defaultYearRange = "$currentYear-$nextYear";

$events = $data['events'] ?? [];
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['currentPage'] ?? 1;

// Helper function to format date
function formatDate($date) {
    $datetime = new DateTime($date);
    return $datetime->format('F j, Y');
}

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
    <title>Manage Events</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet' />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- <link href="../../src/css/custom-calendar.css" rel="stylesheet" /> -->
    <link href="../../src/css/gen.css" rel="stylesheet" />
</head>
<body>
    <!-- top navigation -->
    <!-- <div class="top-nav">
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
    </div> -->

    <!-- sidebar -->
    <!-- <div class="main">
        <div class="sidebar" id="sidebar">
            <div class="logo"></div> 
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div> -->

        <!-- date and time -->
        <div class="content" id="content">
            <div id="datetime">
                <?php echo $currentDateTime; ?>
            </div>

            <h2>Manage Events</h2>
           
            <div class="dropdown">
            <div class="d-flex align-items-center" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <h3>SY <span id="currentYearRange"><?php echo htmlspecialchars($yearRange); ?></span></h3>
                <i class="bi bi-caret-down-fill ms-2" title="Filter by School Year"></i>
            </div>
            <ul class="dropdown-menu" id="yearRangeDropdown" aria-labelledby="dropdownMenuButton">
                <?php foreach ($yearRanges as $range): ?>
                    <li>
                        <span class="dropdown-item" data-year-range="<?= htmlspecialchars($range['year_range'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($range['year_range'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>



        
        <button type="button" class="btn btn-primary" onclick="window.location.href='add_new_event.php?sy=<?= htmlspecialchars($yearRange, ENT_QUOTES, 'UTF-8') ?>'">
            Add New Event
        </button>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#yearRangeModal">
            New SY Calendar
        </button>

     
        <div class="dropdown sort-dropdown">
            <button class="btn btn-secondary dropdown-toggle" id="sortIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Sort order">
                <i class="bi bi-funnel"></i>
            </button>
            <div class="dropdown-menu" aria-labelledby="sortIcon">
                <a class="dropdown-item sort-option" data-order="asc" >Ascending</a>
                <a class="dropdown-item sort-option" data-order="desc" >Descending</a>
            </div>
        </div>



            <!-- Filter Button -->
            <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#filterModal">Filter</button>-->

            
            




            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="eventList">
                    <?php 
                        $itemsPerPage = $data['itemsPerPage'] ?? 10; 
                        $index = ($currentPage - 1) * $itemsPerPage + 1; 
                        foreach ($events as $event): 
                    ?>
                        <tr>
                            <td><?php echo $index++; ?></td> <!-- Display the index and increment it -->
                            <td><?php echo htmlspecialchars($event['title'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($event['description'] ?? ''); ?></td>
                            <td><?php echo isset($event['event_date']) ? formatDate($event['event_date']) : ''; ?></td>
                            <td><?php echo isset($event['end_date']) ? formatDate($event['end_date']) : ''; ?></td>

                            <td>
                                <form action="update_event.php" method="GET" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id'] ?? ''); ?>">
                                    <input type="hidden" name="type" value="<?php echo htmlspecialchars($event['event_type'] ?? ''); ?>">
                                    <input type="hidden" name="year_range" value="<?php echo htmlspecialchars($event['year_range'] ?? ''); ?>">

                                    <button type="submit" class="btn btn-normal" title="Edit event"><i class="bi bi-pencil-square"></i></button>
                                </form>
                                <form id="deleteForm_<?php echo htmlspecialchars($event['id'] ?? ''); ?>" action="../../src/processes/a/delete_event.php" method="POST" style="display:none;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id'] ?? ''); ?>">
                                </form>
                                <button type="button" class="btn btn-danger" title="Delete event" onclick="openVerificationModal('deleteForm_<?php echo htmlspecialchars($event['id'] ?? ''); ?>', 'Confirm Deletion', 'Are you sure you want to delete this event?', 'Delete')">
                                    <i class="bi bi-trash3"></i>
                                </button>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if ($currentPage <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&order=<?php echo $order; ?><?php echo $month !== null ? '&month=' . $month : ''; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <li class="page-item <?php if ($page == $currentPage) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $page; ?>&order=<?php echo $order; ?><?php echo $month !== null ? '&month=' . $month : ''; ?>"><?php echo $page; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&order=<?php echo $order; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>

                </ul>
            </nav>
        </div>
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- <script src="../../src/js/toggleSidebar.js"></script> -->
    <script src="../../src/js/verify.js"></script>
    <script src="../../src/js/yr_select.js"></script>
    <script src="../../src/js/new_sy.js"></script>


    <script>
        $(window).on('load', function() {
            <?php if ($successMessage): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 4500);
            <?php endif; ?>
        });

      

    </script>


</body>
</html>