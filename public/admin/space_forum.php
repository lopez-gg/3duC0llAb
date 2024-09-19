<?php
// space_forum.php

require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php'; // Include session config
require_once __DIR__ . '/../../src/config/db_config.php'; 

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
} else {
    $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
    $_SESSION['grade'] = $grade;

    if (is_numeric($grade) && $grade >= 1 && $grade <= 6) {
        $gradetodisplay = 'Grade ' . intval($grade);
    } elseif (strtolower($grade) === 'sned') {
        $gradetodisplay = strtoupper($grade);
    } else {
        $gradetodisplay = 'Unknown Grade'; 
    }
}

// Set default values for the variables
$csrf_token = $_SESSION['csrf_token'];
$currentDateTime = date('l, d/m/Y h:i:s A'); 
// Pagination settings
$limit = 50; // Posts per page
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// Fetching tasks per grade
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $config['base_url'] . "/src/processes/fetch_forum_posts.php?grade=" . urlencode($grade) . "&limit=" . $limit . "&page=" . $page);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$posts_json = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
$response = json_decode($posts_json, true);

if (isset($response['error'])) {
    echo "<p>Error: " . htmlspecialchars($response['error']) . "</p>";
    $posts = [];
    $totalPages = 1;
} else {
    $posts = $response['posts'];
    $totalPages = $response['totalPages'];
}

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
    <title>Forum for Grade <?= htmlspecialchars($grade) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../../src/css/gen.css" rel="stylesheet">
    <link href="../../src/css/forum_post.css" rel="stylesheet">
    <link href="../../src/css/a/dashb.css" rel="stylesheet">
    
    <style>
        /* Styling for the card header and content */
        .card-header {
            background-color: #f8f9fa;
        }
        .card-body p {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <!-- top navigation -->
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

    <!-- Main content -->
    <div class="main">
        <div class="content" id="content">

            <div class="container mt-5">
                <h1 class="mb-4">Forum for Grade <?= htmlspecialchars($grade) ?></h1>

                <div class="mb-4 text-end">
                    <a href="new_post.php?grade=<?= urlencode($grade) ?>" class="btn btn-primary">Create New Post</a>
                </div>

                <?php if (count($posts) > 0): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between">
                                <div>
                                    <strong><?= htmlspecialchars($post['title']) ?></strong>
                                    <small class="text-muted">by <?= htmlspecialchars($post['username']) ?> on <?= $post['created_at'] ?></small>
                                </div>
                                <div>
                                    <a href="post_view.php?grade=<?=$grade?>&id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-secondary">See full post</a>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <!-- Edit Option -->
                                        <li>
                                            <a class="dropdown-item" href="edit_post.php?grade=<?= $grade ?>&id=<?= $post['id'] ?>">
                                                <i class="bi bi-pencil-square"></i> Edit Post
                                            </a>
                                        </li>
                                        <li>
                                            <form id="delete_post_<?= $post['id'] ?>" action="../../src/processes/a/delete_post.php" method="post" class="dropdown-item p-0">
                                                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                <input type="hidden" name="grade" value="<?= $grade ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0 delete-button" style="display:inline;">
                                                    <i class="bi bi-trash3"></i> Delete Post
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>
                                    <?= strlen($post['content']) > 200 ? substr(htmlspecialchars($post['content']), 0, 200) . '...' : htmlspecialchars($post['content']) ?>
                                </p>
                                <p><small><?= $post['reply_count'] ?> Replies</small></p>
                                <a href="post_view.php?grade=<?=$grade?>&id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary">Comment</a>

                                <!-- Edit and Delete buttons -->
                                
                            </div>
                        </div>
                    <?php endforeach; ?>


                    <!-- Pagination -->
                    <nav>
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="space_forum.php?grade=<?= urlencode($grade) ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php else: ?>
                    <p class="text-center">No posts found for this grade.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    
    <script src='../../src/js/datetime.js'></script>
    <!-- <script src="../../src/js/toggleSidebar.js"></script> -->
    <script src="../../src/js/verify.js"></script>
    <script src="../../src/js/new_sy.js"></script>
    <script src='../../src/js/notification.js'></script>

    <script>
        $(window).on('load', function() {
            <?php if ($successMessage): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 4500);
            <?php endif; ?>
        });
                
        $(document).on('click', '.delete-button', function() {
            var formId = $(this).data('form-id');
            confirmDeleteModal(formId, 'Confirm Deletion', 'Are you sure you want to delete this post?', 'Delete');
        });
    </script>
</body>
</html>
