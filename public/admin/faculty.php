<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/check_new_messages.php'; 

check_access('ADMIN');


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Check if the request is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

// Get filters from request (for AJAX calls)
$grade = $_GET['grade'] ?? null;
$status = $_GET['status'] ?? null;
$search = $_GET['search'] ?? null;


// Initial query to fetch faculty members
$query = "SELECT * FROM users WHERE 1=1";
$params = [];

// Apply grade filter if set
if ($grade) {
    $query .= " AND gradeLevel = :grade";
    $params[':grade'] = $grade;
}

// Apply status filter if set
if ($status) {
    $query .= " AND status = :status";
    $params[':status'] = $status;
}

if ($search) {
    $query .= " AND (firstname LIKE :search OR lastname LIKE :search OR username LIKE :search)";
    $params[':search'] = '%' . $search . '%'; // Use wildcard search
}

// Fetch the faculty members
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$facultyMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If this is an AJAX request, return only the table rows as HTML
if ($isAjax) {
    $output = '';
    foreach ($facultyMembers as $index => $faculty) {
        $output .= '<tr>';
        $output .= '<td>' . ($index + 1) . '</td>';
        $output .= '<td>' . htmlspecialchars($faculty['username']) . '</td>';
        $output .= '<td>' . htmlspecialchars($faculty['firstname']) . '</td>';
        $output .= '<td>' . htmlspecialchars($faculty['lastname']) . '</td>';
        $output .= '<td>' . htmlspecialchars($faculty['gradeLevel']) . '</td>';
        $output .= '<td>' . htmlspecialchars($faculty['section']) . '</td>';
        $output .= '<td>' . htmlspecialchars($faculty['status']) . '</td>';
        $output .= '<td>';
        $output .= '<form method="POST" action="../../src/processes/a/faculty_actions.php" style="display:inline;">';
        $output .= '<input type="hidden" name="faculty_id" value="' . $faculty['id'] . '">';
        $output .= '<button type="submit" name="action" value="edit" class="btn btn-warning btn-sm">Edit</button>';
        $output .= '</form>';
        if ($faculty['status'] != 'deactivated') {
            // Show "Deactivate" if the faculty member is active
            $output .= '<button type="button" class="btn btn-danger btn-sm verifyDeactivationButton" data-faculty-id="' . $faculty['id'] . '">Deactivate</button>';
        } elseif ($faculty['status'] === 'deactivated') {
            // Show "Activate" if the faculty member is deactivated
            $output .= '<button type="button" class="btn btn-success btn-sm verifyActivationButton" data-faculty-id="' . $faculty['id'] . '">Activate</button>';
        } echo '</td>';
        $output .= '</td>';
        $output .= '</tr>';
    }

    echo $output; 
    exit;
}

$currentDateTime = date('l, d/m/Y h:i:s A'); 
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
    <title>Manage Faculty</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="../../src/css/a/h-e-gen.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../src/css/message.css">
    <style>
        .table {
            max-width: 1800px;
        }
    </style>
</head>
<body>
    <?php include '../nav-sidebar-temp.php' ?>
    <div class="content" id="content">
        <div class="container mt-4">
            <h2>Manage Faculty Members</h2>

            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="gradeFilter" class="form-select" onchange="filterFaculty()">
                        <option value="" <?= !$grade ? 'selected' : '' ?>>All Grades</option>
                        <option value="Grade 1" <?= $grade === 'Grade 1' ? 'selected' : '' ?>>Grade 1</option>
                        <option value="Grade 2" <?= $grade === 'Grade 2' ? 'selected' : '' ?>>Grade 2</option>
                        <option value="Grade 3" <?= $grade === 'Grade 3' ? 'selected' : '' ?>>Grade 3</option>
                        <option value="Grade 4" <?= $grade === 'Grade 4' ? 'selected' : '' ?>>Grade 4</option>
                        <option value="Grade 5" <?= $grade === 'Grade 5' ? 'selected' : '' ?>>Grade 5</option>
                        <option value="Grade 6" <?= $grade === 'Grade 6' ? 'selected' : '' ?>>Grade 6</option>
                        <option value="SNED" <?= $grade === 'SNED' ? 'selected' : '' ?>>SNED</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select" onchange="filterFaculty()">
                        <option value="" <?= !isset($status) || $status === '' ? 'selected' : '' ?>>All Statuses</option>
                        <option value="active" <?= isset($status) && $status === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= isset($status) && $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="deactivated" <?= isset($status) && $status === 'deactivated' ? 'selected' : '' ?>>Deactivated</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" id="searchFaculty" class="form-control" placeholder="Search all by name..." onkeyup="searchFaculty()" value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-primary" onclick="window.location.href='add_user.php'">Add Faculty</button>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Grade Level</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="facultyList">
                    <?php
                    if (!empty($facultyMembers)) {
                        foreach ($facultyMembers as $index => $faculty) {
                            echo '<tr>';
                            echo '<td>' . ($index + 1) . '</td>';
                            echo '<td>' . htmlspecialchars($faculty['username']) . '</td>';
                            echo '<td>' . htmlspecialchars($faculty['firstname']) . '</td>';
                            echo '<td>' . htmlspecialchars($faculty['lastname']) . '</td>';
                            echo '<td>' . htmlspecialchars($faculty['gradeLevel']) . '</td>';
                            echo '<td>' . htmlspecialchars($faculty['section']) . '</td>';
                            echo '<td>' . htmlspecialchars($faculty['status']) . '</td>';
                            echo '<td>';
                            echo '<form method="POST" action="../../src/processes/a/faculty_actions.php" style="display:inline;">';
                            echo '<input type="hidden" name="faculty_id" value="' . $faculty['id'] . '">';
                            echo '<button type="submit" name="action" value="edit" class="btn btn-warning btn-sm">Edit</button>';
                            echo '</form>';
                            
                            if ($faculty['status'] != 'deactivated') {
                                // Show "Deactivate" if the faculty member is active
                                echo '<button type="button" class="btn btn-danger btn-sm verifyDeactivationButton" data-faculty-id="' . $faculty['id'] . '">Deactivate</button>';
                            } elseif ($faculty['status'] === 'deactivated') {
                                // Show "Activate" if the faculty member is deactivated
                                echo '<button type="button" class="btn btn-success btn-sm verifyActivationButton" data-faculty-id="' . $faculty['id'] . '">Activate</button>';
                            } echo '</td>';
                            
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="8">No faculty members found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    
    <script src='../../src/js/datetime.js'></script>
    <script src="../../src/js/toggleSidebar.js"></script>
    <script src="../../src/js/verify.js"></script>
    <script src="../../src/js/faculty.js"></script>
    <script src='../../src/js/notification.js'></script>
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

        $(document).on('click', '.verifyDeactivationButton, .verifyActivationButton', function() {
            $(this).prop('disabled', true);  // Disable button to prevent duplicate clicks
        });

    </script>
</body>
</html>
