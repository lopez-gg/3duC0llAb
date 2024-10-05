<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 
include '../../src/processes/fetch_sy.php';
include '../../src/processes/fetch_e_type.php';

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$calendr = 'calendar';
$dashb = '';
$my_space = '';
$calendr = '';
$gen_forum = '';
$faculty = '';

$month = isset($_GET['month']) ? (int)$_GET['month'] : null;
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$yearRange = isset($_GET['year_range']) ? $_GET['year_range'] : getCurrentYearRange();

// Get base URL from the configuration
$baseURL = $config['base_url'] . '/src/processes/a/fetch_manage_events.php';

// Capture current parameters (remove 'events' from params)
$params = [
    'page' => $currentPage,
    'order' => $order,
    'month' => $month,
    'year_range' => $yearRange,
    'search' => isset($_GET['search']) ? $_GET['search'] : ''
];

// Build URL with current parameters
$url = $baseURL . '?' . http_build_query(array_filter($params)); // Build query without nulls

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

// Output the raw JSON response for debugging
// header('Content-Type: application/json');
// echo $response; // Check what this outputs

$data = json_decode($response, true);
if ($data === null) {
    die('Error decoding JSON: ' . json_last_error_msg());
}

// Use $data['events'], $data['currentPage'], etc. for further processing

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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/message.css">
    <link href="../../src/css/gen.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../src/css/action_nav.css">
</head>
<body>

    <?php include '../nav-sidebar-temp.php'?>
        <div class="content" id="content">

            <h2>Calendar > Manage Events</h2>

            <hr>

            <section class="actions-section">
                <div class="right-section-actions">
                    <div class="rs-a">
                        <button class="btn-sort dropdown-toggle" id="sortIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Sort order">
                            <i class="bi bi-funnel"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="sortIcon">
                            <a class="dropdown-item sort-option" data-order="asc">Ascending</a>
                            <a class="dropdown-item sort-option" data-order="desc">Descending</a>
                        </div>
                    </div>
                    <div class="rs-a">
                        <button class="btn-filter dropdown-toggle" id="filterIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Filter tasks">
                            <i class="bi bi-filter"></i> SY <span id="currentYearRange"><?php echo htmlspecialchars($yearRange); ?></span>
                        </button>
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
                    <div class="rs-a">
                        <button class="btn-add-sy"  data-bs-toggle="modal" data-bs-target="#yearRangeModal" title="Create new SY Calendar">
                            <i class="bi bi-calendar-plus"></i>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="filterIcon">
                            <a class="dropdown-item filter-option" data-progress="">All</a>
                            <a class="dropdown-item filter-option" data-progress="pending">Pending</a>
                            <a class="dropdown-item filter-option" data-progress="in_progress">In Progress</a>
                            <a class="dropdown-item filter-option" data-progress="completed">Completed</a>
                        </div>
                    </div>
                </div>
                <div class="left-section-actions">
                    <div class="ls-a">
                        <div class="search-bar">
                            <input type="text" class="searchBox" id="searchEvents" placeholder="Search events..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button id="searchButton"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    <div class="ls-a">
                        <div class="btn-add">
                            <a href="add_new_event.php?sy=<?= htmlspecialchars($yearRange, ENT_QUOTES, 'UTF-8') ?>" title="Add new event for the current SY"><i class="bi bi-plus-circle"></i></a>
                        </div>
                    </div>
                </div>
            </section>
            <hr>

            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th></th>
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
                            <td>
                                <div 
                                    class="event_type" 
                                    title="<?php echo htmlspecialchars($event['event_type']); ?>" 
                                    style="height:25px; width:25px; background-color:<?php echo htmlspecialchars($event['color']); ?>; border-radius: 50%;">
                                </div>
                            </td>
                            <td><?php echo $index++; ?></td> 
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
                                <form id="deleteForm_<?php echo htmlspecialchars($event['id'] ?? ''); ?>" action="../../src/processes/a/delete_event.php" method="POST" >
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id'] ?? ''); ?>">
                                </form>
                                <button type="button" class="btn btn-danger" title="Delete event" onclick="openVerificationModal('deleteForm_<?php echo htmlspecialchars($event['id'] ?? ''); ?>', 'Confirm Deletion', 'Are you sure you want to delete this event?', 'Delete', 'manage_events.php', '1')">
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
                    <a class="page-link" href="?page=<?php echo $currentPage; ?>&order=<?php echo $order; ?>&year_range=<?php echo $yearRange; ?>&search=<?php echo htmlspecialchars($search); ?>">
                        <?php echo $currentPage; ?>
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

    <script src="../../src/js/toggleSidebar.js"></script>
    <script src="../../src/js/verify.js"></script>
    <script src="../../src/js/yr_select.js"></script>
    <script src="../../src/js/new_sy.js"></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/message.js'></script>


    <script>
        $(window).on('load', function() {
            <?php if ($successMessage): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 4500);
            <?php endif; ?>
        });

    // Handle searching when search button is clicked
    $('#searchButton').on('click', function () {
        const searchQuery = $('#searchEvents').val();
        var yearRange = $('#currentYearRange').text(); // Get the current year range

        var newUrl = "?search=" + encodeURIComponent(searchQuery) + "&year_range=" + encodeURIComponent(yearRange) + "&order=<?php echo $order; ?>&page=1";
        window.location.href = newUrl; // Update with your actual page
    });

    // Handle "Enter" key in search input
    $('#searchEvents').on('keypress', function (e) {
        if (e.which == 13) {
            $('#searchButton').click();
        }
    });


    </script>


</body>
</html>