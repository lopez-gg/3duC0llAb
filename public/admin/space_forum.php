<?php
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';


check_access('ADMIN');

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}else {
    $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
    $_SESSION['grade'] = $grade;

    if (is_numeric($grade) && $grade >= 1 && $grade <= 6) {
        // If the grade is a number between 1 and 6, display it as 'Grade X'
        $gradetodisplay = 'Grade ' . intval($grade);
    } elseif (strtolower($grade) === 'sned') {
        // If the grade is 'sned', display it as 'SNED'
        $gradetodisplay = strtoupper($grade);
    } else {
        // Handle cases where the grade is not valid (optional)
        $gradetodisplay = 'Unknown Grade'; // or handle error accordingly
    }
   
}

// Set default values for the variables
$currentDateTime = date('l, d/m/Y h:i:s A'); 
// Handle Pagination Variables
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Initialize $currentPage

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($gradetodisplay); ?> Forum</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link href="../../src/css/gen.css" rel="stylesheet">
    <link href="../../src/css/a/dashb.css" rel="stylesheet">
</head>
<body>
    <div class="top-nav">
        <div class="left-section">
            <button class="sidebar-toggle-button" onclick="toggleSidebar()">☰</button>
            <div class="app-name">EduCollab</div>
            <div id="datetime"><?php echo htmlspecialchars($currentDateTime); ?></div>
        </div>

        <div class="right-section">
            <div class="notification-bell">
                <i class="bi bi-bell-fill"></i>
                <span class="notification-count">0</span>
            </div>
            
            <div class="notification-dropdown">
                <ul class="notification-list"> 
                    <!-- Notifications will be appended here by JavaScript -->
                </ul>
                <button class="see-more" style="display: none;">See More...</button>
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
    </div> 

    <!-- sidebar -->
    <div class="main">
        <div class="sidebar" id="sidebar">
            <div class="logo"></div> 
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="calendar.php">Calendar</a>
            </div>
        </div>

        <div class="content" id="content">
            <section class=main-sec id='sec-one'>
                <h2><?php echo htmlspecialchars($gradetodisplay); ?> Forum</h2>
            </section>

            <section class=main-sec id='sec-one'>
                <!-- Form to add new forum post -->
                <form id="newPostForm">
                    <input type="hidden" name="grade" value="<?php echo htmlspecialchars($grade); ?>">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Post</button>
                </form>
            </section>
            <hr>

            <!-- Forum Posts -->
            <div id="forumPosts"></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    
    <script src='../../src/js/datetime.js'></script>
    <!-- <script src="../../src/js/toggleSidebar.js"></script> -->
    <script src="../../src/js/verify.js"></script>
    <script src="../../src/js/new_sy.js"></script>
    <script src='../../src/js/notification.js'></script>
    <script>
    $(document).ready(function() {
        const grade = '<?php echo htmlspecialchars($grade); ?>';

        // Fetch forum posts
        function fetchPosts() {
            $.getJSON('../../src/processes/fetch_forum_posts.php?grade=' + grade, function(data) {
                let forumHtml = '';
                data.forEach(post => {
                    forumHtml += `
                        <div class="card my-3">
                            <div class="card-body">
                                <h5 class="card-title">${post.title}</h5>
                                <p class="card-text">${post.content}</p>
                                <small class="text-muted">Posted by ${post.username} on ${post.created_at}</small>
                                <hr>
                                <div class="replies">
                                    <h6>Replies</h6>`;
                    post.replies.forEach(reply => {
                        forumHtml += `
                            <p><strong>${reply.username}:</strong> ${reply.reply_content} <small class="text-muted">on ${reply.created_at}</small></p>`;
                    });
                    forumHtml += `
                                    <form class="replyForm">
                                        <input type="hidden" name="post_id" value="${post.id}">
                                        <textarea name="reply_content" class="form-control mb-2" rows="2" required></textarea>
                                        <button type="submit" class="btn btn-sm btn-secondary">Reply</button>
                                    </form>
                                </div>
                            </div>
                        </div>`;
                });
                $('#forumPosts').html(forumHtml);
            });
        }

        // Add new post
        $('#newPostForm').submit(function(e) {
            e.preventDefault();
            $.post('../../src/processes/add_forum_post.php', $(this).serialize(), function(data) {
                if (data.success) {
                    fetchPosts(); // Reload posts after adding
                    $('#newPostForm')[0].reset(); // Clear form
                }
            }, 'json');
        });

        // Add reply to post
        $(document).on('submit', '.replyForm', function(e) {
            e.preventDefault();
            $.post('../../src/processes/add_forum_reply.php', $(this).serialize(), function(data) {
                if (data.success) {
                    fetchPosts(); // Reload posts after adding reply
                }
            }, 'json');
        });

        // Fetch posts on page load
        fetchPosts();
    });
    </script>
</body>
</html>
