<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/event_form.css">

</head>
<body>
    <div class="top-nav">
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
                <a href="dashboard.php#">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div>

        <div class="content" id="content">
            <h2>Add Event</h2>

            <form id="eventsForm" action="../../src/processes/a/add_event.php" method="POST">
                <div id="form-container">
                    <div class="event-form-group">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" name="events[0][title]" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea class="form-control" name="events[0][description]" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="start">Start Date:</label>
                            <input type="date" class="form-control" name="events[0][start]" required>
                        </div>
                        <div class="form-group">
                            <label for="end">End Date:</label>
                            <input type="date" class="form-control" name="events[0][end]">
                        </div>
                        <hr>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit All Events</button>
            </form>

            <button type="button" class="btn btn-secondary" id="addNewEvent">Add New Event</button>
        </div>

        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
        <script src="../../src/js/toggleSidebar.js"></script>
        <script src="../../src/js/verify.js"></script>
        
        <script>
            $(document).ready(function() {
                var eventCount = 1; // Start from 1 since the initial form is already present

                $('#addNewEvent').click(function() {
                    var newForm = `
                        <div class="event-form-group">
                            <div class="form-group">
                                <label for="title">Title:</label>
                                <input type="text" class="form-control" name="events[${eventCount}][title]" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description:</label>
                                <textarea class="form-control" name="events[${eventCount}][description]" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="start">Start Date:</label>
                                <input type="date" class="form-control" name="events[${eventCount}][start]" required>
                            </div>
                            <div class="form-group">
                                <label for="end">End Date:</label>
                                <input type="date" class="form-control" name="events[${eventCount}][end]">
                            </div>
                            <hr>
                        </div>
                    `;
                    $('#form-container').append(newForm);
                    eventCount++;
                });
            });
        </script>
    </div>
</body>
</html>
