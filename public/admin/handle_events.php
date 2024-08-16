<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';


date_default_timezone_set('Asia/Manila'); 
$currentDateTime = date('l, d/m/Y h:i:s A'); 

// Default values
$events = [];
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = 1;

// Generate the base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$baseURL = $protocol . $host . '/EduCollab/src/processes/a/fetch_manage_events.php';

// Fetch paginated events
$url = $baseURL . '?page=' . $currentPage;
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

$data = json_decode($response, true);

if ($data === null) {
    die('Error decoding JSON: ' . json_last_error_msg());
}

$events = $data['events'] ?? [];
$totalPages = $data['totalPages'] ?? 1;
$currentPage = $data['currentPage'] ?? 1;

// Helper function to format date
function formatDate($date) {
    $datetime = new DateTime($date);
    return $datetime->format('F j, Y');
}

echo 'Session ID in handle_events.php: ' . session_id() . '<br>';
echo '<pre>';
print_r($_SESSION); // Debug output to check session data
echo '</pre>';

$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
include '../display_message.php';
unset($_SESSION['success_message']);
unset($_SESSION['verification_message']);




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet' />
    <link href="../../src/css/custom-calendar.css" rel="stylesheet" />
    <link href="../../src/css/gen.css" rel="stylesheet" />
</head>
<body>
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
    </div>

    <div class="main">
        <div class="sidebar" id="sidebar">
            <div class="logo"></div> 
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div> -->

        <div class="content" id="content">
            <div id="datetime">
                <?php echo $currentDateTime; ?>
            </div>

            <h2>Manage Events</h2>

            <button type="button" class="btn btn-primary" onclick="window.location.href='add_new_event.php'">Add New Event</button>

            <!-- Test Button to Trigger Modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#successModal">
                Test Modal
            </button>

            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="eventList">
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['title'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($event['description'] ?? ''); ?></td>
                            <td><?php echo isset($event['start']) ? formatDate($event['start']) : ''; ?></td>
                            <td><?php echo isset($event['end']) ? formatDate($event['end']) : ''; ?></td>
                            <td>
                                <form action="update_event.php" method="GET" style="display:inline;">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id'] ?? ''); ?>">
                                    <button type="submit"><i class="bi bi-pencil-square"></i></button>
                                </form>
                                <form action="../../src/processes/a/delete_event.php" method="POST" style="display:inline;" onsubmit="return confirmEventDeletion();">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id'] ?? ''); ?>">
                                    <button type="submit"><i class="bi bi-trash3"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if ($currentPage <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <li class="page-item <?php if ($page == $currentPage) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="../../src/js/toggleSidebar.js"></script>
    <script src="../../src/js/verify.js"></script>
    <script src="../../src/js/display_message.js"></script>

    <script>
        $(window).on('load', function() {
            <?php if ($successMessage): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 4500);
            <?php endif; ?>

            <?php if ($verificationMessage): ?>
                $('#verificationModal').modal('show');
            <?php endif; ?>
        });
    </script>


</body>
</html>
