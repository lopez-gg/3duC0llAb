<?php
// request_appointment.php
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';

check_access('USER');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../public/login.php');
    exit;
}

$faculty = null;
if (isset($_GET['f_id'])) {
    $funame = $_GET['f_id'];

    // Fetch faculty details
    try {
        $query = "SELECT id, username, firstname, lastname FROM users WHERE username = :f_uname";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':f_uname', $funame, PDO::PARAM_STR);
        $stmt->execute();
        
        $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$faculty) {
            die('Faculty not found');
        }
    } catch (PDOException $e) {
        die('Database error: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Appointment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/form.css">
    <link rel="stylesheet" href="../../src/css/message.css">

    <style>
        #facultySearchResults {
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            display: none; 
        }

        .search-result-item {
            padding: 10px;
            cursor: pointer;
            background-color: #fff;
        }

        .search-result-item:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>

    <div class="content" id="content">
    <h2>Request Appointment</h2>
    <form action="../../src/processes/process_appointment.php" method="POST">
        <input type="hidden" name="faculty_id" value="<?= $faculty['id'] ?? ''; ?>"> <!-- Use null coalescing operator -->

        <div class="form-container">
            <div class="mb-3">
                <label for="facultyUsername" class="form-label">Faculty Username</label>
                <input type="text" class="form-control" id="facultyUsername" name="faculty_username" placeholder="Search by username" autocomplete="off" required>
                <div id="facultySearchResults"></div>
                <input type="hidden" id="facultyIdInput" name="faculty_id"> <!-- Hidden input for faculty ID -->
            </div>

            <div class="mb-3">
                <label for="appointmentTitle" class="form-label">Request Title</label>
                <input type="text" class="form-control" id="appointmentTitle" name="appointment_title" placeholder="Enter a title for your request" required>
            </div>

            <div class="mb-3">
                <label for="appointmentDate" class="form-label">Appointment Date</label>
                <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
            </div>

            <div class="mb-3">
                <label for="appointmentTime" class="form-label">Appointment Time</label>
                <input type="time" class="form-control" id="appointmentTime" name="appointment_time" required>
            </div>

            <div class="mb-3">
                <label for="additionalNotes" class="form-label">Additional Notes</label>
                <textarea class="form-control" id="additionalNotes" name="additional_notes" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Request</button>
        </div>
    </form>
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
    <script src='../../src/js/verify.js'></script>
    <script src='../../src/js/reminder.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const facultyUsernameInput = document.getElementById('facultyUsername');
            const facultySearchResults = document.getElementById('facultySearchResults');
            const facultyIdInput = document.getElementById('facultyIdInput');

            // Function to handle live search
            facultyUsernameInput.addEventListener('input', function() {
                const query = this.value.trim();

                if (query.length > 0) {
                    fetch('../../src/processes/search_users.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `query=${encodeURIComponent(query)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.users && data.users.length > 0) {
                            facultySearchResults.innerHTML = ''; // Clear previous results
                            data.users.forEach(user => {
                                const resultItem = document.createElement('div');
                                resultItem.classList.add('search-result-item');
                                resultItem.textContent = `${user.username} (${user.firstname} ${user.lastname})`;
                                resultItem.dataset.id = user.id; // Store the user ID in the dataset
                                resultItem.dataset.username = user.username; // Store the username in the dataset

                                // Event listener for when a search result is clicked
                                resultItem.addEventListener('click', function() {
                                    facultyUsernameInput.value = this.dataset.username;
                                    facultyIdInput.value = this.dataset.id; // Set the faculty ID in the hidden input
                                    facultySearchResults.style.display = 'none'; // Hide the search results
                                });

                                facultySearchResults.appendChild(resultItem);
                            });

                            facultySearchResults.style.display = 'block'; // Show search results
                        } else {
                            facultySearchResults.innerHTML = '<div class="search-result-item">No users found</div>';
                            facultySearchResults.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                } else {
                    facultySearchResults.style.display = 'none'; // Hide the search results if query is empty
                }
            });
        });
    </script>
</body>
</html>
