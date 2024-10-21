<?php
// space_forum.php

require_once __DIR__ . '/../../src/config/access_control.php'; 
require_once __DIR__ . '/../../src/config/session_config.php'; // Include session config
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

// Check if the user is admin
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$grade = 'general';
$_SESSION['grade'] = $grade;

// Set default values for the variables
$gen_forum ='gen_forum';
$csrf_token = $_SESSION['csrf_token'];
$grade = 'general';
$currentDateTime = date('l, d/m/Y h:i:s A'); 
// Pagination settings

$forum = isset($_GET['forum']) ? trim($_GET['forum']) : 'general';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

require_once __DIR__ . '/../../src/processes/fetch_general_forum_posts.php';
$forumPostsData = fetch_forum_posts($forum, $limit, $page);

if (isset($forumPostsData['error'])) {
    echo "<p>Error: " . htmlspecialchars($forumPostsData['error']) . "</p>";
    exit;
}

$posts = $forumPostsData['posts'] ?? [];
$totalPages = $forumPostsData['totalPages'] ?? 1;


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
    <title>PSCS General Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../../src/css/gen.css" rel="stylesheet">
    <link href="../../src/css/forum_post.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/message.css">
    
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
    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
            <div class="container">
                <h1 class="mb-4">PSCS General Forum</h1>

                <div class="mb-4 text-end">

                    <a href="new_post.php?grade=general" class="btn btn-primary">Create New Post</a>
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
                    <p class="text-center">No posts found for this forum.</p>
                <?php endif; ?>
            </div>
        </div>
        
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    
    <script src='../../src/js/datetime.js'></script>
    <script src="../../src/js/toggleSidebar.js"></script>
    <script src="../../src/js/verify.js"></script>
    <script src="../../src/js/new_sy.js"></script>
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
                
        $(document).on('click', '.delete-button', function() {
            var formId = $(this).data('form-id');
            confirmDeleteModal(formId, 'Confirm Deletion', 'Are you sure you want to delete this post?', 'Delete');
        });
    </script>
</body>
</html>
