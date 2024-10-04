<?php
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php'; 
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'desc'; // Default sort order
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Set default values for the variables
$currentDateTime = date('l, d/m/Y h:i:s A'); 
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Initialize $currentPage
$itemsPerPage = $data['itemsPerPage'] ?? 10; 
$index = ($currentPage - 1) * $itemsPerPage + 1; 

$params = [
    'grade' => $grade,
    'order' => $sortOrder,
    'search' => $searchKeyword,
    'page' => $currentPage
];

// Build the query string
$queryString = http_build_query($params);

// Fetch archived tasks
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['base_url'] . "/src/processes/a/fetch_archived_tasks.php?" . $queryString);
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

$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
include '../display_mod.php';
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Tasks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../src/css/gen.css" rel="stylesheet">
    <link href="../../src/css/a/dashb.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/message.css">
</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>
        <div class="content" id="content">
            <section class="main-sec" id="sec-one">
                <h2>Archived Tasks</h2>
            </section>

            <section class="main-sec" id="sec-two">
                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search tasks" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>

                <div class="sort-dropdown mb-3">
                    <button class="btn btn-secondary dropdown-toggle" id="sortIcon" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Sort order">
                        <i class="bi bi-funnel"></i> Sort
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="sortIcon">
                        <li><a class="dropdown-item sort-option" data-order="asc">Ascending</a></li>
                        <li><a class="dropdown-item sort-option" data-order="desc">Descending</a></li>
                    </ul>
                </div>
            </section>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Assigned By</th>
                        <th>Assigned To</th>
                        <th>Grade</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Due Date</th>
                        <th>Completed At</th>
                        <th>Deleted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tasks['tasks'])): ?>
                        <tr>
                            <td colspan="11">No archived tasks found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tasks['tasks'] as $task): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['id'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['title'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['assignedBy'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['assignedTo'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['grade'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['progress'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['status'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['created_at'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['due_date'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['completed_at'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($task['deleted_at'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <nav aria-label="Task pagination">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if ($currentPage <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>&<?php echo $queryString; ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                        <li class="page-item <?php if ($page == $currentPage) echo 'active'; ?>">
                            <a class="page-link" href="?page=<?php echo $page; ?>&<?php echo $queryString; ?>"><?php echo $page; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php if ($currentPage >= $totalPages) echo 'disabled'; ?>">
                        <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>&<?php echo $queryString; ?>" aria-label="Next">
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

    
    <script src='../../src/js/message.js'></script>
    
    <script>
        $(document).ready(function() {
            $('.sort-option').on('click', function() {
                var order = $(this).data('order');
                var url = new URL(window.location.href);
                url.searchParams.set('order', order);
                window.location.href = url.href;
            });
        });
    </script>
</body>
</html>
