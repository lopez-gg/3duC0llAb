<?php
// Include database and session configuration
require_once __DIR__ . '/../../src/config/db_config.php'; // Ensure this includes your PDO setup
require_once __DIR__ . '/../../src/config/session_config.php'; // Ensure session management is configured

$user_id = $_SESSION['user_id']; // Currently logged-in user

// Search, sort, and filter logic
$search = $_GET['search'] ?? '';
$sort_order = $_GET['sort_order'] ?? 'asc';
$filter_month = $_GET['filter_month'] ?? ''; // Get the selected month
$filter_status = $_GET['filter_status'] ?? ''; // Get the selected status
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$filter_label = '';

$active_filter = $_GET['filter'] ?? '';
$active_month = $_GET['filter_month'] ?? '';
$active_status = $_GET['filter_status'] ?? '';

// SQL base query
$sql = "SELECT a.*, 
               u1.username AS sender, 
               u2.username AS receiver
        FROM appointments a
        JOIN users u1 ON a.user_id = u1.id
        JOIN users u2 ON a.faculty_id = u2.id
        WHERE (u1.username LIKE :search OR a.additional_notes LIKE :search)";


// Add conditions based on the filter
if ($filter === 'received') {
    $sql .= " AND a.faculty_id = :faculty_id"; 
} elseif ($filter === 'sent') {
    $sql .= " AND a.user_id = :user_id"; 
}

// Handle month filtering
if (!empty($filter_month)) {
    $sql .= " AND MONTH(a.appointment_date) = :filter_month";
}

// Handle status filtering
if (!empty($filter_status)) {
    $sql .= " AND a.status = :filter_status";
}

// Handle requests sent to the user
if (!empty($filter_to_user)) {
    $sql .= " AND a.faculty_id = :user_id"; 
}

// Handle requests sent by the user
if (!empty($filter_by_user)) {
    $sql .= " AND a.user_id = :user_id"; 
}

// Sorting
$sql .= " ORDER BY a.appointment_date $sort_order";

// Prepare and bind parameters
$stmt = $pdo->prepare($sql);
$search_param = "%{$search}%";
$stmt->bindParam(':search', $search_param, PDO::PARAM_STR);

if (!empty($filter_month)) {
    $stmt->bindParam(':filter_month', $filter_month, PDO::PARAM_STR);
}

if (!empty($filter_status)) {
    $stmt->bindParam(':filter_status', $filter_status, PDO::PARAM_STR);
}

if (!empty($filter_to_user) || !empty($filter_by_user)) {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

if ($filter === 'received') {
    $stmt->bindParam(':faculty_id', $user_id, PDO::PARAM_INT); 
} elseif ($filter === 'sent') {
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

// Execute the query and fetch results
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/action_nav.css">
    <link rel="stylesheet" href="../../src/css/message.css">

</head>
<body>
<?php include '../nav-sidebar-temp.php'?>

    <div class="content" id="content">

        <section class="actions-section">
            <div class="right-section-actions">
                <!-- Sort Dropdown -->
                <div class="rs-a">
                    <button class="btn-sort dropdown-toggle" id="sortIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Sort order">
                        <i class="bi bi-funnel"></i>
                        <span id="sortOrderText"></span>
                    </button>
                    
                    <div class="dropdown-menu" aria-labelledby="sortIcon">
                        <a class="dropdown-item sort-option" data-order="asc">Ascending</a>
                        <a class="dropdown-item sort-option" data-order="desc">Descending</a>
                    </div>
                </div>

                <!-- Status Filter Dropdown -->
                <div class="rs-a">
                    <button class="btn-filter dropdown-toggle" id="filterIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Filter by status">
                        <i class="bi bi-filter"></i>
                        <span id="statusText"><?= !empty($active_status) ? ucfirst($active_status) : 'All'; ?></span> 
                    </button>
                    
                    <div class="dropdown-menu" aria-labelledby="filterIcon">
                        <a class="dropdown-item filter-status-option" data-status="">All</a>
                        <a class="dropdown-item filter-status-option" data-status="pending">Pending</a>
                        <a class="dropdown-item filter-status-option" data-status="approved">Approved</a>
                        <a class="dropdown-item filter-status-option" data-status="declined">Declined</a>
                    </div>
                </div>

                <!-- Month Filter Dropdown -->
                <div class="rs-a">
                    <button class="btn-filter dropdown-toggle" id="monthFilterIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Filter by month">
                        <i class="bi bi-calendar3"></i>
                        <span id="monthText">
                            <?php
                            $monthNames = [
                                '', 'January', 'February', 'March', 'April', 'May', 'June', 
                                'July', 'August', 'September', 'October', 'November', 'December'
                            ];
                            echo $monthNames[(int)$active_month];
                            ?>
                        </span>
                    </button>
                    
                    <div class="dropdown-menu" aria-labelledby="monthFilterIcon">
                        <a class="dropdown-item month-option" data-month="">All</a> <!-- Reset month filter -->
                        <a class="dropdown-item month-option" data-month="01">January</a>
                        <a class="dropdown-item month-option" data-month="02">February</a>
                        <a class="dropdown-item month-option" data-month="03">March</a>
                        <a class="dropdown-item month-option" data-month="04">April</a>
                        <a class="dropdown-item month-option" data-month="05">May</a>
                        <a class="dropdown-item month-option" data-month="06">June</a>
                        <a class="dropdown-item month-option" data-month="07">July</a>
                        <a class="dropdown-item month-option" data-month="08">August</a>
                        <a class="dropdown-item month-option" data-month="09">September</a>
                        <a class="dropdown-item month-option" data-month="10">October</a>
                        <a class="dropdown-item month-option" data-month="11">November</a>
                        <a class="dropdown-item month-option" data-month="12">December</a>
                        
                    </div>
                </div>

                <div class="rs-a">
                    <button class="btn-filter dropdown-toggle" id="sentReceivedFilterIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Filter sent/received requests">
                        <span id="sentReceivedText">
                            <?php
                            if ($active_filter === 'received') {
                                echo 'Received';
                            } elseif ($active_filter === 'sent') {
                                echo 'Sent';
                            } else {
                                echo 'All';
                            }
                            ?>
                        </span>
                    </button>
                    
                    <div class="dropdown-menu" aria-labelledby="sentReceivedFilterIcon">
                        <a class="dropdown-item filter-option" data-filter="">All</a> 
                        <a class="dropdown-item filter-option" data-filter="received">Received</a>
                        <a class="dropdown-item filter-option" data-filter="sent">Sent</a>
                    </div>
                </div>
            </div>

            <div class="left-section-actions">
                <!-- Search Bar -->
                <div class="ls-a">
                    <div class="search-bar position-relative">
                        <input type="text" class="searchBox" id="appointmentSearch" placeholder="Search appointments..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button id="clearSearchButton" class="btn" style="display: flex;"><i class="bi bi-x-circle"></i></button>
                        <button id="searchButton"><i class="bi bi-search"></i></button>
                        </div>
                </div>

                <!-- Add Button -->
                <div class="ls-a">
                    <div class="btn-add">
                        <a href="request_appointment.php?_personal" id="taskEdit" title="Add new appointment"><i class="bi bi-plus-circle"></i></a>
                    </div>
                </div>

                
            </div>
        </section>


        <!-- Table displaying the appointments -->
        <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Requested Date</th>
                <th>Requested Time</th>
                <th>Status</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($appointments)): ?>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['sender']); ?></td>
                        <td><?= htmlspecialchars($appointment['receiver']); ?></td>
                        <td><?= date('Y-m-d', strtotime($appointment['appointment_date'])); ?></td>
                        <td><?= date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                        <td><?= htmlspecialchars($appointment['status']); ?></td>
                        <td><?= htmlspecialchars($appointment['additional_notes']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No appointments found</td>
                </tr>
            <?php endif; ?>
        </tbody>

        </table>
    </div>
</div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
    <script src='../../src/js/message.js'></script>
    

    <script>
       $('#sortOrderText').text('<?= ucfirst($sort_order); ?>');

        // Handle sort option selection
        document.querySelectorAll('.sort-option').forEach(function(item) {
            item.addEventListener('click', function() {
                let order = this.getAttribute('data-order');
                let url = new URL(window.location.href);
                url.searchParams.set('sort_order', order);
                setTimeout(() => {
                    window.location.href = url.toString();
                }, 100); 
            });
        });

        // Handle month filter selection
        document.querySelectorAll('.month-option').forEach(function(item) {
            item.addEventListener('click', function() {
                let month = this.getAttribute('data-month');
                let url = new URL(window.location.href);
                url.searchParams.delete('filter_month');
                url.searchParams.set('filter_month', month);
                setTimeout(() => {
                    window.location.href = url.toString();
                }, 100); 
            });
        });

        // Handle status filter selection
        document.querySelectorAll('.filter-status-option').forEach(function(item) {
            item.addEventListener('click', function() {
                let status = this.getAttribute('data-status');
                let url = new URL(window.location.href);
                url.searchParams.delete('filter_status');
                url.searchParams.set('filter_status', status);
                setTimeout(() => {
                    window.location.href = url.toString();
                }, 100); 
            });
        });

        // Handle search bar submission
        $(document).ready(function() {
            const searchInput = $('#appointmentSearch');
            const clearSearchButton = $('#clearSearchButton');

            // Show or hide the clear button based on input value
            searchInput.on('input', function() {
                if ($(this).val()) {
                    clearSearchButton.show();
                } else {
                    clearSearchButton.hide();
                }
            });

            // Clear the search input when the clear button is clicked
            clearSearchButton.on('click', function() {
                searchInput.val('');
                $(this).hide();
                const url = new URL(window.location.href);
                url.searchParams.delete('search');
                window.location.href = url.toString();
            });

            // Handle search bar submission
            $('#searchButton').on('click', function() {
                const searchQuery = searchInput.val();
                const url = new URL(window.location.href);
                if (searchQuery) {
                    url.searchParams.set('search', searchQuery);
                } else {
                    url.searchParams.delete('search');
                }
                window.location.href = url.toString();
            });
        });


        $(document).ready(function() {
            function updateFilterIcon(filter) {
                let iconSpan = document.getElementById('sentReceivedFilterIcon');
                if (filter === 'received') {
                    iconSpan.innerHTML = `<img src="../../src/img/ic/inbox-in.svg" style="height:20px; width: auto;" alt="Received">`;
                } else if (filter === 'sent') {
                    iconSpan.innerHTML = `<img src="../../src/img/ic/inbox-out.svg" style="height:20px; width: auto;" alt="Sent">`;
                } else {
                    iconSpan.innerHTML = 'All'; 
                }
            }

            // Set initial filter based on URL
            const urlParams = new URLSearchParams(window.location.search);
            const currentFilter = urlParams.get('filter') || 'all';
            updateFilterIcon(currentFilter);

            // Handle filter option selection
            $('.filter-option').on('click', function() {
                const filter = $(this).data('filter');
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('filter', filter);
                updateFilterIcon(filter);
                setTimeout(() => {
                    window.location.href = url.toString();
                }, 100); 
            });
        });


    </script>

</body>
</html>
