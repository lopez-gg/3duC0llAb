<?php
// Include database and session configuration
require_once __DIR__ . '/../../src/config/db_config.php'; // Ensure this includes your PDO setup
require_once __DIR__ . '/../../src/config/session_config.php'; // Ensure session management is configured

$user_id = $_SESSION['user_id']; // Currently logged-in user

$view = $_GET['view'] ?? 'appointments'; 
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

if ($view === 'appointments') {
    // SQL query for appointments
    $sql = "SELECT a.*, 
                   u1.username AS sender, 
                   u2.username AS receiver
            FROM appointments a
            JOIN users u1 ON a.requestor = u1.id
            JOIN users u2 ON a.requestee = u2.id
            WHERE (a.requestor = :user_id OR a.requestee = :user_id)
            AND (u1.username LIKE :search OR u2.username LIKE :search OR a.additional_notes LIKE :search)";

    // Add the filters for appointments
    if ($filter === 'received') {
        $sql .= " AND a.requestee = :user_id";
    } elseif ($filter === 'sent') {
        $sql .= " AND a.requestor = :user_id";
    }

    if (!empty($filter_month)) {
        $sql .= " AND MONTH(a.appointment_date) = :filter_month";
    }

    if (!empty($filter_status)) {
        $sql .= " AND a.status = :filter_status";
    }

    // Sorting and execution
    $sql .= " ORDER BY a.appointment_date $sort_order";
    $stmt = $pdo->prepare($sql);
} elseif ($view === 'change_requests') {
    // SQL query for change requests
    $sql = "SELECT cr.*, 
                   u1.username AS requested_by_user, 
                   u2.username AS requested_to_user
            FROM change_requests cr
            JOIN users u1 ON cr.requested_by = u1.id
            JOIN users u2 ON cr.requested_to = u2.id 
            WHERE (cr.requested_by = :user_id OR cr.requested_to = :user_id)
            AND (u1.username LIKE :search OR u2.username LIKE :search OR cr.notes LIKE :search)";

    // Add filters for change requests if needed
    if ($filter === 'received') {
        $sql .= " AND cr.requested_to = :user_id";
    } elseif ($filter === 'sent') {
        $sql .= " AND cr.requested_by = :user_id";
    }

    if (!empty($filter_status)) {
        $sql .= " AND cr.status = :filter_status";
    }

    // Sorting and execution
    $sql .= " ORDER BY cr.created_at $sort_order";
    $stmt = $pdo->prepare($sql);
}

// Bind parameters and execute
$search_param = "%{$search}%";
$stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

if (!empty($filter_month)) {
    $stmt->bindParam(':filter_month', $filter_month, PDO::PARAM_STR);
}

if (!empty($filter_status)) {
    $stmt->bindParam(':filter_status', $filter_status, PDO::PARAM_STR);
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC); 

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

        <div class="view-toggle">
            <button id="appointmentViewBtn" class="btn btn-primary">Appointment Requests</button>
            <button id="changeRequestViewBtn" class="btn btn-secondary">Change Requests</button>
        </div>

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

        <!-- Table for Appointments -->
        <?php if ($view === 'appointments'): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Title</th>
                        <th>Requested Date</th>
                        <th>Requested Time</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['sender']); ?></td>
                            <td><?= htmlspecialchars($appointment['receiver']); ?></td>
                            <td><?= htmlspecialchars($appointment['appointment_title']); ?></td>
                            <td><?= htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?= htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td>
                                <?php if ($appointment['requestee'] == $user_id && $appointment['status'] != 'approved'):?>
                                        <select class="status-select" style="border:none;" data-appointment-id="<?= $appointment['id']; ?>">
                                            <option value="pending" <?= $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?= $appointment['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="declined" <?= $appointment['status'] === 'declined' ? 'selected' : ''; ?>>Declined</option>
                                        </select>
                                    <?php else: ?>
                                        <?= htmlspecialchars($appointment['status']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($appointment['additional_notes']); ?></td>
                                <td>
                                <?php if ($appointment['requestee'] == $user_id || ($appointment['requestor'] == $user_id && $appointment['status'] === 'approved')) : ?>
                                    <button class="btn btn-warning requestApptChangeBtn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#requestApptChangeModal"
                                            data-appointment-id="<?= $appointment['id']; ?>"
                                            data-appointment-current-date="<?= $appointment['appointment_date']; ?>" 
                                            data-appointment-current-time="<?= $appointment['appointment_time']; ?>">
                                        Request Change
                                    </button>

                                <?php else: ?>
                                    <button class="btn btn-primary editBtn" data-id="<?= $appointment['id']; ?>">
                                        Edit
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        
        <!-- Table for Change Requests -->
        <?php elseif ($view === 'change_requests'): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Requested by</th>
                        <th>Requested to</th>
                        <th>Title</th>
                        <th>New Date</th>
                        <th>New Time</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($results as $change_request): ?>
                    <?php
                        // Fetch the appointment title for the current change request
                        $appointment_id = $change_request['appointment_id'];
                        $stmt_appointment = $pdo->prepare("SELECT appointment_title FROM appointments WHERE id = :appointment_id");
                        $stmt_appointment->bindParam(':appointment_id', $appointment_id, PDO::PARAM_INT);
                        $stmt_appointment->execute();
                        $appointment_title = $stmt_appointment->fetchColumn();
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($change_request['requested_by_user']); ?></td>
                        <td><?= htmlspecialchars($change_request['requested_to_user']); ?></td>
                        <td><?= htmlspecialchars($appointment_title); ?></td>
                        <td><?= htmlspecialchars($change_request['new_date']); ?></td>
                        <td><?= htmlspecialchars($change_request['new_time']); ?></td>
                        <td>
                            <?php if ($change_request['requested_to'] == $user_id && $change_request['status'] != 'approved'): ?>
                                <select class="change-request-status-select" style="border:none;" data-change-request-id="<?= $change_request['id']; ?>">
                                    <option value="pending" <?= $change_request['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?= $change_request['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="declined" <?= $change_request['status'] === 'declined' ? 'selected' : ''; ?>>Declined</option>
                                </select>
                            <?php else: ?>
                                <?= htmlspecialchars($change_request['status']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($change_request['notes']); ?></td>
                        <td>
                            <?php if ($change_request['requested_to'] == $user_id || ($change_request['requested_by'] == $user_id && $change_request['status'] === 'approved')): ?>
                                <button class="btn btn-warning requestCrChangeBtn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#requestCrChangeModal" 
                                    data-cr-id="<?= $change_request['id']; ?>"
                                    data-cr-current-date="<?= $change_request['new_date']; ?>" 
                                    data-cr-current-time="<?= $change_request['new_time']; ?>">
                                    Request Change
                                </button>
                            <?php else: ?>
                                <button class="btn btn-primary editBtn" data-id="<?= $change_request['id']; ?>">
                                    Edit
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        <?php endif; ?>


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
                    iconSpan.innerHTML = `<img src="../../src/img/ic/inbox-in.svg" style="height:20px; width: auto; margin:5px;" alt="Received">`;
                } else if (filter === 'sent') {
                    iconSpan.innerHTML = `<img src="../../src/img/ic/inbox-out.svg" style="height:20px; width: auto; margin:5px;" alt="Sent">`;
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
                    window.location.href = currentUrl.toString();
                }, 100); 
            });
        });

        //Appointment change request
        document.querySelectorAll('.requestApptChangeBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-appointment-id');
                const currentDate = this.getAttribute('data-appointment-current-date');
                const currentTime = this.getAttribute('data-appointment-current-time');

                // Set values in the modal fields
                document.getElementById('appointmentId').value = appointmentId;
                document.getElementById('newDate').value = currentDate;  // Default to current date
                document.getElementById('newTime').value = currentTime;  // Default to current time
            });
        });

        //change_request request
        document.querySelectorAll('.requestCrChangeBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-cr-id');
                const currentDate = this.getAttribute('data-cr-current-date');
                const currentTime = this.getAttribute('data-cr-current-time');

                // Set values in the modal fields
                document.getElementById('crappointmentId').value = appointmentId;
                document.getElementById('crnewDate').value = currentDate;  // Default to current date
                document.getElementById('crnewTime').value = currentTime;  // Default to current time
            });
        });



        $(document).ready(function() {
            // Handle status change
            $('.status-select').on('change', function() {
                const appointmentId = $(this).data('appointment-id');
                const newStatus = $(this).val();

                // Send AJAX request to update the status
                $.ajax({
                    url: '../../src/processes/update_appointment_status.php', // Update this to your actual update script
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        id: appointmentId,
                        status: newStatus
                    },
                    success: function(response) {
                    console.log("Raw response:", response); // Check the raw response first

                    try {
                        let message = '';
                        
                        if (response.success) {
                            if (newStatus === 'approved') {
                                message = 'Change request approved and appointment updated!';
                            } else {
                                message = 'Change request status updated!';
                            }
                        } else {
                            message = 'Error updating status.';
                        }

                        // Assign the message to the successModal body
                        $('#successModal .modal-body').text(message);
                        
                        // Show the modal
                        $('#successModal').modal('show');
                        
                        // Hide the modal after 3 seconds
                        setTimeout(function() {
                            $('#successModal').modal('hide');
                        }, 3000);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.error('Response:', response);
                        $('#successModal .modal-body').text('Failed to update status due to invalid response.');
                        $('#successModal').modal('show');
                        setTimeout(function() {
                            $('#successModal').modal('hide');
                        }, 3000);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error updating status:', textStatus, errorThrown);
                    $('#successModal .modal-body').text('Failed to update status.');
                    $('#successModal').modal('show');
                    setTimeout(function() {
                        $('#successModal').modal('hide');
                    }, 3000);
                }
                });
            });
        });

        document.getElementById('appointmentViewBtn').addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('view', 'appointments');
            window.location.href = url.toString();
        });

        document.getElementById('changeRequestViewBtn').addEventListener('click', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('view', 'change_requests');
            window.location.href = url.toString();
        });

        $(document).ready(function() {
        // Handle change request status change
        $('.change-request-status-select').on('change', function() {
            const changeRequestId = $(this).data('change-request-id');
            const newStatus = $(this).val();

            // Send AJAX request to update the change request status
            $.ajax({
                url: '../../src/processes/update_change_request_status.php', // Update to your actual script
                method: 'POST',
                data: {
                    id: changeRequestId,
                    status: newStatus
                },
                success: function(response) {
                    console.log("Raw response:", response); // Check the raw response first

                    try {
                        let message = '';
                        
                        if (response.success) {
                            if (newStatus === 'approved') {
                                message = 'Change request approved and appointment updated!';
                            } else {
                                message = 'Change request status updated!';
                            }
                        } else {
                            message = 'Error updating status.';
                        }

                        // Assign the message to the successModal body
                        $('#successModal .modal-body').text(message);
                        
                        // Show the modal
                        $('#successModal').modal('show');
                        
                        // Hide the modal after 3 seconds
                        setTimeout(function() {
                            $('#successModal').modal('hide');
                        }, 3000);
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.error('Response:', response);
                        $('#successModal .modal-body').text('Failed to update status due to invalid response.');
                        $('#successModal').modal('show');
                        setTimeout(function() {
                            $('#successModal').modal('hide');
                        }, 3000);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error updating status:', textStatus, errorThrown);
                    $('#successModal .modal-body').text('Failed to update status.');
                    $('#successModal').modal('show');
                    setTimeout(function() {
                        $('#successModal').modal('hide');
                    }, 3000);
                }
            });
        });
    });
    </script>

</body>
</html>
