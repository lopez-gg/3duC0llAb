<?php
// post_view.php

require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


if ($post_id <= 0) {
    echo "Invalid post ID.";
    exit;
}

$edited_reply_id = isset($_GET['edited_reply']) ? (int)$_GET['edited_reply'] : null;

try {
    // Fetch the post details
    $postQuery = "SELECT fp.id, fp.title, fp.content, fp.created_at, u.username
                  FROM forum_posts fp
                  JOIN users u ON fp.user_id = u.id
                  WHERE fp.id = :post_id";
    $postStmt = $pdo->prepare($postQuery);
    $postStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $postStmt->execute();
    $post = $postStmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "Post not found.";
        exit;
    }

    // Fetch replies for the post (including nested replies), most recent first
    $repliesQuery = "SELECT fr.id, fr.reply_content, fr.created_at, u.username, fr.parent_id, fr.user_id
                     FROM forum_replies fr
                     JOIN users u ON fr.user_id = u.id
                     WHERE fr.post_id = :post_id
                     ORDER BY fr.created_at DESC";
    $repliesStmt = $pdo->prepare($repliesQuery);
    $repliesStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $repliesStmt->execute();
    $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);

    function displayReplies($replies, $parent_id = null, $level = 0) {
        $html = '';
        foreach ($replies as $reply) {
            if ($reply['parent_id'] == $parent_id) {
                $html .= '<li class="list-group-item reply-item mb-3" style="margin-left: ' . ($level * 20) . 'px;" data-reply-id="' . $reply['id'] . '">';
                $html .= '<p><strong>' . htmlspecialchars($reply['username']) . '</strong> <small class="text-muted">' . $reply['created_at'] . '</small></p>';
                $html .= '<p>' . nl2br(htmlspecialchars($reply['reply_content'])) . '</p>';
                
                // Only show edit and delete buttons for replies by the current user
                if ($reply['user_id'] == $_SESSION['user_id']) {
                    $html .= '<button class="btn btn-warning btn-sm edit-button" data-reply-id="' . $reply['id'] . '" data-reply-content="' . htmlspecialchars($reply['reply_content']) . '">Edit</button> ';
                    $html .= '<button class="btn btn-danger btn-sm delete-button" data-reply-id="' . $reply['id'] . '">Delete</button>';
                }

                $html .= '<button class="btn btn-link reply-button" data-reply-id="' . $reply['id'] . '" data-reply-username="' . htmlspecialchars($reply['username']) . '" data-reply-content="' . htmlspecialchars($reply['reply_content']) . '">Reply</button>';
                $html .= displayReplies($replies, $reply['id'], $level + 1);
                $html .= '</li>';
            }
        }
        return $html;
    }

} catch (PDOException $e) {
    log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
    echo "An error occurred.";
    exit;
}

$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
include '../display_mod.php';
unset($_SESSION['success_message']);
?>

<?php
// Check if there is an edited reply ID
$edited_reply_id = isset($_GET['edited_reply']) ? (int)$_GET['edited_reply'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../src/css/gen.css" rel="stylesheet">
    <link href="../../src/css/forum_post.css" rel="stylesheet">
    <style>
        /* Add styles for the flash effect */
        .highlight {
            background-color: #d1e7dd; /* Light green background */
            transition: background-color 0.5s ease;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Post Card -->
        <div class="post-card post-container">
            <div class="post-header">
                <div class="post-title"><?= htmlspecialchars($post['title']) ?></div>
                <p class="post-meta">by <?= htmlspecialchars($post['username']) ?> on <?= $post['created_at'] ?></p>
            </div>
            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
        </div>

        <!-- Partition Line -->
        <div class="partition"></div>

        <!-- Replies Section -->
        <div class="replies-card replies-container">
            <h5>Replies</h5>
            <?php if (count($replies) > 0): ?>
                <ul class="list-group">
                    <?= displayReplies($replies) ?>
                </ul>
            <?php else: ?>
                <p class="no-replies">No replies yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Fixed Reply Form -->
    <div class="reply-form reply-form-container">
        <div class="reply-context-container" id="reply-context-container">
            <p id="reply-context">Replying to:</p>
            <span id="reply-context-cancel" class="reply-context-cancel">Cancel</span>
        </div>
        <form id="replyForm" action="../../src/processes/submit_reply.php" method="post">
            <div class="input-group">
                <textarea class="form-control" name="reply_content" rows="1" placeholder="Write your reply..." required></textarea>
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <input type="hidden" name="parent_id" id="parent_id" value="NULL">
                <input type="hidden" name="action_type" id="action_type" value="reply">
                <input type="hidden" name="reply_id" id="reply_id" value="0">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <script src="../../src/js/fetch_post.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Handle reply editing scroll and highlight
            const editedReplyId = <?= json_encode($edited_reply_id) ?>;
            if (editedReplyId) {
                const replyElement = document.querySelector(`.reply-item[data-reply-id="${editedReplyId}"]`);
                if (replyElement) {
                    replyElement.classList.add('highlight');
                    replyElement.scrollIntoView({ behavior: 'smooth' });

                    // Remove highlight after a short delay
                    setTimeout(() => {
                        replyElement.classList.remove('highlight');
                        const url = new URL(window.location.href);
                        url.searchParams.delete('edited_reply');
                        window.history.replaceState({}, '', url);
                    }, 3500); // Adjust the delay as needed
                }
            }
        });
    </script>
</body>
</html>
