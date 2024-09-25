<?php
require_once __DIR__ . '/../../src/config/access_control.php'; // Include access control script
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

$events = require_once __DIR__ . '/../../src/processes/fetch_upcoming_events.php'; 


//Check if the user is an admin
check_access('ADMIN');

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/form.css">

    <title>Add New User</title>
</head>
<body>

    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
            <h1>Add New Account</h1>
            <div class="form-container">
                <form action="../../src/processes/a/add_user_process.php" method="post">
                    <div class="user-form-group">
                        <?php 
                        // Form fields array
                        $formFields = [
                            'username' => 'Username',
                            'firstname' => 'First Name',
                            'lastname' => 'Last Name',
                            'section' => 'Section',
                            'password' => 'Password'
                        ];
                        ?>

                        <?php foreach ($formFields as $name => $label): ?>
                            <div class="form-group">
                                <label for="<?= $name ?>"><?= $label ?>:</label>
                                <input type="<?= $name === 'password' ? 'password' : 'text' ?>" 
                                    id="<?= $name ?>" 
                                    name="<?= $name ?>" 
                                    required 
                                    <?= $name === 'section' ? 'value="Subject Teacher"' : '' ?>>
                            </div>
                        <?php endforeach; ?>

                        <div class="form-group">
                            <label for="gradeLevel">Grade Level:</label>
                            <select name="gradeLevel" id="gradeLevel" required>
                                <?php
                                $gradeLevels = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'SNED'];
                                foreach ($gradeLevels as $level): ?>
                                    <option value="<?= $level ?>"><?= $level ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Register</button>
                    <button type="button" class="btn btn-secondary" 
                            onclick="openDiscardChangesModal('', '', 'Are you sure you want to discard changes?', 'Discard')">Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
    <script src='../../src/js/datetime.js'></script>
    <script>
        function openDiscardChangesModal() {
            $('#discardChangesModal').modal('show');
        }

        $('#confirmDiscardButton').on('click', function() {
            window.location.href = 'faculty.php'; 
        });
    </script>
</body>
</html>
