<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';

check_access('ADMIN');

$currentDateTime = date('l, d/m/Y h:i:s A'); 
$successTitle = $_SESSION['success_title'] ?? null;
$successMessage = $_SESSION['success_message'] ?? null;
$verificationMessage = $_SESSION['verification_message'] ?? null;
include '../display_mod.php';
unset($_SESSION['success_message']);

// Fetch initial faculty data
$facultyData = file_get_contents('../../src/processes/a/fetch_faculty.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Faculty</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="../../src/css/a/h-e-gen.css" rel="stylesheet" />
</head>
<body>
    <div class="container mt-4">
        <h2>Manage Faculty Members</h2>

        <div class="row mb-3">
            <div class="col-md-3">
                <select id="gradeFilter" class="form-select" onchange="filterFaculty()">
                    <option value="">All Grades</option>
                    <option value="Grade 1">Grade 1</option>
                    <option value="Grade 2">Grade 2</option>
                    <option value="Grade 3">Grade 3</option>
                    <option value="Grade 4">Grade 4</option>
                    <option value="Grade 5">Grade 5</option>
                    <option value="Grade 6">Grade 6</option>
                    <option value="SNED">SNED</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="statusFilter" class="form-select" onchange="filterFaculty()">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="deactivated">Deactivated</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" id="searchFaculty" class="form-control" placeholder="Search by name..." onkeyup="searchFaculty()">
            </div>
            <div class="col-md-2 text-end">
                <button class="btn btn-primary" onclick="window.location.href='add_new_faculty.php'">Add Faculty</button>
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
                <?php echo $facultyData; ?>
            </tbody>
        </table>
    </div>

    <script src="../../src/js/faculty.js"></script>
    <script>
        $(document).ready(function() {
            // Initial load
            filterFaculty();
        });
    </script>
</body>
</html>
