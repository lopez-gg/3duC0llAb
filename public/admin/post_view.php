<?php
// post_view.php

require_once __DIR__ . '/../../src/config/db_config.php'; // Database config
require_once __DIR__ . '/../../src/config/session_config.php'; // Include session config

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// Get the post ID from the query string
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($post_id <= 0) {
    echo "Invalid post ID.";
    exit;
}

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

    // Fetch replies for the post (including nested replies)
    $repliesQuery = "SELECT fr.id, fr.reply_content, fr.created_at, u.username, fr.parent_id
                     FROM forum_replies fr
                     JOIN users u ON fr.user_id = u.id
                     WHERE fr.post_id = :post_id
                     ORDER BY fr.created_at ASC";
    $repliesStmt = $pdo->prepare($repliesQuery);
    $repliesStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $repliesStmt->execute();
    $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Recursive function to display replies and nested replies
    function displayReplies($replies, $parent_id = null, $level = 0) {
        $html = '';
        foreach ($replies as $reply) {
            if ($reply['parent_id'] == $parent_id) {
                $html .= '<li class="list-group-item reply-item mb-3" style="margin-left: ' . ($level * 20) . 'px;" data-reply-id="' . $reply['id'] . '">';
                $html .= '<p><strong>' . htmlspecialchars($reply['username']) . '</strong> <small class="text-muted">' . $reply['created_at'] . '</small></p>';
                $html .= '<p>' . nl2br(htmlspecialchars($reply['reply_content'])) . '</p>';
                // Reply button
                $html .= '<button class="btn btn-link reply-button" data-reply-id="' . $reply['id'] . '" data-reply-username="' . htmlspecialchars($reply['username']) . '" data-reply-content="' . htmlspecialchars($reply['reply_content']) . '">Reply</button>';
                $html .= displayReplies($replies, $reply['id'], $level + 1); // Recursively display replies to this reply
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../src/css/gen.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .container {
            flex: 1;
        }
        .post-card, .replies-card {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
            background-color: white;
        }
        .post-card {
            margin-bottom: 10px;
        }
        .partition {
            border-top: 2px solid #e9ecef;
            margin: 40px 0;
        }
        .replies-card {
            padding-bottom: 70px; /* Ensure space for the fixed reply form */
        }
        .reply-form {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #ffffff;
            padding: 15px;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
        }
        .reply-form textarea {
            resize: none;
        }
        .list-group-item {
            background-color: #f9f9f9;
        }
        .post-header {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .post-title {
            font-size: 24px;
            font-weight: bold;
            color: #343a40;
        }
        .post-meta {
            font-size: 14px;
            color: #6c757d;
        }
        .no-replies {
            color: #6c757d;
        }
        .input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }
        .input-group .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .reply-context-container {
            display: none; /* Hide by default */
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            background-color: #f1f1f1;
            position: relative; /* For positioning the cancel button */
        }
        .reply-context-container p {
            margin: 0;
            font-size: 14px;
            color: #333;
        }
        .reply-context-cancel {
            position: absolute;
            top: 5px;
            right: 10px;
            cursor: pointer;
            color: #007bff;
            font-size: 14px;
            font-weight: bold;
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
        <form action="../../src/processes/submit_reply.php" method="post">
            <div class="input-group">
                <textarea class="form-control" name="reply_content" rows="1" placeholder="Write your reply..." required></textarea>
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <input type="hidden" name="parent_id" id="parent_id" value="NULL"> <!-- To store parent reply ID -->
                <input type="hidden" name="at" value="am">
                <button type="submit" class="btn btn-primary">Reply</button>
            </div>
        </form>
    </div>

    <script>
        // When a reply button is clicked, update the hidden parent_id field and context
        document.querySelectorAll('.reply-button').forEach(button => {
            button.addEventListener('click', function() {
                const replyId = this.getAttribute('data-reply-id');
                const replyUsername = this.getAttribute('data-reply-username');
                const replyContent = this.getAttribute('data-reply-content');
                
                // Update the reply form context
                const replyContext = document.getElementById('reply-context');
                replyContext.innerHTML = `Replying to: <strong>${replyUsername}</strong><br><em>${replyContent.replace(/\n/g, '<br>')}</em>`;
                
                // Set the parent_id for the reply
                document.getElementById('parent_id').value = replyId;

                // Show the context container and scroll to the reply form
                document.getElementById('reply-context-container').style.display = 'block';
                document.querySelector('.reply-form-container').scrollIntoView({ behavior: 'smooth' });
            });
        });

        // Clear the reply context when "Cancel" is clicked
        document.getElementById('reply-context-cancel').addEventListener('click', function() {
            document.getElementById('reply-context-container').style.display = 'none';
            document.getElementById('parent_id').value = 'NULL';
            document.querySelector('.reply-form-container').scrollIntoView({ behavior: 'smooth' });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
