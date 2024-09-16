<?php
// space_forum.php

require_once __DIR__ . '/../../src/config/session_config.php'; // Includes session and security configurations
require_once __DIR__ . '/../../src/config/db_config.php'; // Includes database connection

// Get the grade from the URL parameter
$grade = $_GET['grade'] ?? null;

if (!$grade) {
    echo 'Grade not specified.';
    exit;
}

// Pagination settings
$limit = 50; // Posts per page
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// Include the script to fetch forum posts for the specific grade
require_once __DIR__ . '/../../src/processes/fetch_forum_posts.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum for Grade <?= htmlspecialchars($grade) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <small class="text-muted">by User <?= $post['user_id'] ?> on <?= $post['created_at'] ?></small>
                    </div>
                    <div>
                        <a href="view_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-secondary">See full post</a>
                    </div>
                </div>
                <div class="card-body">
                    <p>
                        <?= strlen($post['content']) > 200 ? substr(htmlspecialchars($post['content']), 0, 200) . '...' : htmlspecialchars($post['content']) ?>
                    </p>

                    <!-- Toggle Replies Button -->
                    <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReplies<?= $post['id'] ?>" aria-expanded="false" aria-controls="collapseReplies<?= $post['id'] ?>">
                        View Replies
                    </button>
                    
                    <!-- Replies Section (Initially hidden) -->
                    <div class="collapse mt-3" id="collapseReplies<?= $post['id'] ?>">
                        <div class="card card-body">
                           
                            <!-- // Fetch replies for this post
                            require __DIR__ . '/../../src/processes/fetch_replies.php'; // Fetch replies based on post ID
                            if (count($replies) > 0):
                                foreach ($replies as $reply): ?>
                                    <div class="border-bottom mb-3">
                                        <p><strong>User <n?= $reply['user_id'] ?>:</strong> <h?= htmlspecialchars($reply['reply_content']) ?></p>
                                        <small class="text-muted">Posted on <h?= $reply['created_at'] ?></small>
                                    </div>
                                <k?php endforeach;
                            else: ?>
                                <p>No replies yet.</p>
                            <g?php endif;  -->
                        </div>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
