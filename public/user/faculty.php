<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

// Check if the user is admin
check_access('USER');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
} else {
    $userID = $_SESSION['user_id'];
}


$faculty = 'faculty';

// Handle Pagination Variables
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$itemsPerPage = 10; 
$index = ($currentPage - 1) * $itemsPerPage + 1;
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
$progress = isset($_GET['progress']) ? $_GET['progress'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch tasks from internal function
require_once __DIR__ . '/../../src/processes/a/fetch_my_tasks.php';
$tasksData = fetch_my_tasks($userID, $order, $progress, $search, $currentPage, $itemsPerPage);


if (isset($tasksData['error'])) {
    echo "<p>Error: " . htmlspecialchars($tasksData['error']) . "</p>";
    exit;
}

$tasks = $tasksData['tasks'] ?? [];
$totalPages = $tasksData['totalPages'] ?? 1;


$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = $_SESSION['success_message'] ?? null;
include '../display_mod.php';
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Space</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../src/css/gen.css" rel="stylesheet">
        <link rel="stylesheet" href="../../src/css/faculty.css">
        <link rel="stylesheet" href="../../src/css/message.css">
    </head>
    <body>
        <?php include '../nav-sidebar-temp.php'?>
            <div class="content" id="content">
            <h2>PSCS Faculty</h2>

           <div class="faculty-con">
            <div id="grade1" class="faculty-container">
                    <h3>Grade 1</h3>
                    <ul id="grade1List"></ul>
                </div>

                <div id="grade2" class="faculty-container">
                    <h3>Grade 2</h3>
                    <ul id="grade2List"></ul>
                </div>

                <div id="grade3" class="faculty-container">
                    <h3>Grade 3</h3>
                    <ul id="grade3List"></ul>
                </div>

                <div id="grade4" class="faculty-container">
                    <h3>Grade 4</h3>
                    <ul id="grade4List"></ul>
                </div>
                <div id="grade5" class="faculty-container">
                    <h3>Grade 5</h3>
                    <ul id="grade5List"></ul>
                </div>
                <div id="grade6" class="faculty-container">
                    <h3>Grade 6</h3>
                    <ul id="grade6List"></ul>
                </div>
                <div id="sned" class="faculty-container">
                    <h3>SNED</h3>
                    <ul id="snedList"></ul>
                </div>
           </div>






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
            $(window).on('load', function() {
                <?php if ($successMessage): ?>
                    $('#successModal').modal('show');
                    setTimeout(function() {
                        $('#successModal').modal('hide');
                    }, 4500);
                <?php endif; ?>
            });

            // Function to load faculty members and populate containers
            function loadFaculty() {
                fetch('../../src/processes/u/fetch_faculty.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            // console.error('Error fetching user details:', data.error);
                            // alert(data.error); 
                        } else {
                            // Populate each container by gradeLevel
                            data.forEach(faculty => {
                                const listItem = document.createElement('li');
                                listItem.textContent = `${faculty.firstname} ${faculty.lastname}`;
                                listItem.classList.add('faculty-item');
                                listItem.setAttribute('data-user-id', faculty.id);

                                // Add click event listener to each list item
                                listItem.addEventListener('click', function() {
                                    const userId = this.getAttribute('data-user-id');
                                    // console.log(`Clicked user ID: ${userId}`);
                                    showUserDetails(userId);  // Pass the user_id to the showUserDetails function
                                });

                                // Append to the corresponding grade container
                                switch (faculty.gradeLevel) {
                                    case 'Grade 1':
                                        document.getElementById('grade1List').appendChild(listItem);
                                        break;
                                    case 'Grade 2':
                                        document.getElementById('grade2List').appendChild(listItem);
                                        break;
                                    case 'Grade 3':
                                        document.getElementById('grade3List').appendChild(listItem);
                                        break;
                                    case 'Grade 4':
                                        document.getElementById('grade4List').appendChild(listItem);
                                        break;
                                    case 'Grade 5':
                                        document.getElementById('grade5List').appendChild(listItem);
                                        break;
                                    case 'Grade 6':
                                        document.getElementById('grade6List').appendChild(listItem);
                                        break;
                                    case 'SNED':
                                        document.getElementById('snedList').appendChild(listItem);
                                        break;
                                }
                            });
                        }
                    })
                    .catch(error => console.error('Error fetching faculty data:', error));
            }


            window.onload = loadFaculty;

            // Function to fetch and show user details in the modal
            function showUserDetails(userId) {
                // console.log(`Fetching details for user ID: ${userId}`);
                fetch(`../../src/processes/u/fetch_faculty_details.php?f_id=${userId}`)  
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error fetching user details:', data.error);
                        } else {
                            document.getElementById('userName').textContent = `${data.firstname} ${data.lastname}`;
                            document.getElementById('userUsername').textContent = data.username;
                            document.getElementById('userGradeLevel').textContent = data.gradeLevel;
                            document.getElementById('userSection').textContent = data.section;
                            document.getElementById('userStatus').textContent = data.status;

                            const requestAppointmentBtn = document.getElementById('requestAppointmentBtn');
                            requestAppointmentBtn.onclick = function() {
                                const funame = `${data.username}`;
                                window.location.href = `request_appointment.php?f_id=${encodeURIComponent(funame)}`;
                            };

                            // Show the modal
                            var userDetailsModal = new bootstrap.Modal(document.getElementById('userDetailsModal'));
                            userDetailsModal.show();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

        </script>
    </body>
</html>