<?php
// post_view.php

require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';

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
    } else if(strtolower($grade === 'general')){
        $gradetodisplay = 'PSCS General ';
    }else {
        $gradetodisplay = 'Unknown Grade'; 
    }
}

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


if ($post_id <= 0) {
    echo "Invalid post ID.";
    exit;
}

$edited_reply_id = isset($_GET['edited_reply']) ? (int)$_GET['edited_reply'] : null;
$new_reply_id = isset($_GET['new_reply']) ? (int)$_GET['new_reply'] : null;
$csrf_token = $_SESSION['csrf_token'];

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
                $html .= '<div class="reply-header">';
                $html .= '<p><strong>' . htmlspecialchars($reply['username']) . '</strong> <small class="text-muted">' . $reply['created_at'] . '</small></p>';
                $html .= '<p>' . nl2br(htmlspecialchars($reply['reply_content'])) . '</p>';
        
                // Only show edit and delete buttons for replies by the current user
                if ($reply['user_id'] == $_SESSION['user_id']) {
                    $html .= '<div class="reply-actions">';
                    $html .= '<button class="btn btn-warning btn-sm edit-button" data-reply-id="' . $reply['id'] . '" data-reply-content="' . htmlspecialchars($reply['reply_content']) . '">
                                <i class="bi bi-pencil-square"></i>
                              </button> ';
                    $html .= '<button class="btn btn-danger btn-sm delete-button" data-reply-id="' . $reply['id'] . '">
                                <i class="bi bi-trash3"></i>
                              </button>';
                    $html .= '</div>';
                }
        
                $html .= '</div>'; // Close reply-header div
        
                // Footer for positioning the reply button
                $html .= '<div class="reply-footer">';
                $html .= '<button class="btn btn-link reply-button" data-reply-id="' . $reply['id'] . '" data-reply-username="' . htmlspecialchars($reply['username']) . '" data-reply-content="' . htmlspecialchars($reply['reply_content']) . '">
                            <i class="bi bi-reply"></i>
                          </button>';
                $html .= '</div>'; // Close reply-footer div
                
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../src/css/gen.css" rel="stylesheet">
    <link href="../../src/css/forum_post.css" rel="stylesheet">

</head>
<body>
    <h1 class="mb-4"><?= htmlspecialchars($gradetodisplay) ?> Forum </h1>

    <div class="container mt-5">
        <!-- Post Card -->
        <div class="post-card post-container">
            <div class="post-header">
                <div class="post-title"><?= htmlspecialchars($post['title']) ?></div>
                <p class="post-meta">by <?= htmlspecialchars($post['username']) ?> on <?= $post['created_at'] ?></p>
                <div class="post-actions">
                    <a href="edit_post.php?grade=<?= $grade?>&id=<?= $post['id'] ?>" title='Edit post' class="edit-button">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form id="delete_post_<?= $post['id'] ?>" action="../../src/processes/a/delete_post.php" method="post" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="grade" value="<?= $grade ?>">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <button type="button" class="delete-button" data-form-id="delete_post_<?= $post['id'] ?>">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </div>
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
                <textarea class="form-control" name="reply_content" rows="1" placeholder="Add a comment..." required></textarea>
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <input type="hidden" name="parent_id" id="parent_id" value="NULL">
                <input type="hidden" name="action_type" id="action_type" value="reply">
                <input type="hidden" name="reply_id" id="reply_id" value="0">
                <button type="submit" class="btn btn-primary" >Submit</button>
            </div>
        </form>
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../src/js/verify.js"></script>
    
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

            const newReplyId = <?= json_encode($new_reply_id) ?>;
            if (newReplyId) {
                const newReplyElement = document.querySelector(`.reply-item[data-reply-id="${newReplyId}"]`);
                if (newReplyElement) {
                    newReplyElement.classList.add('highlight');
                    newReplyElement.scrollIntoView({ behavior: 'smooth' });

                    // Remove highlight and URL parameter after a short delay
                    setTimeout(() => {
                        newReplyElement.classList.remove('highlight');
                        const url = new URL(window.location.href);
                        url.searchParams.delete('new_reply');
                        window.history.replaceState({},'', url);
                    }, 3500);
                }
            }

        });

         // Handle reply button clicks
        $(document).on('click', '.reply-button', function() {
            console.log('Reply button clicked');
            var replyId = $(this).data('reply-id');
            var replyUsername = $(this).data('reply-username');
            var replyContent = $(this).data('reply-content');
            var characterLimit = 200;
            
            // Log the values
            // console.log('Reply ID:', replyId);
            // console.log('Reply Username:', replyUsername);
            // console.log('Reply Content:', replyContent);

            function formatReplyContent(content, limit) {
                if (content.length > limit) {
                    // Trim the content and add an ellipsis
                    content = content.substring(0, limit) + '...';
                }
                // Replace newlines with <br> for proper formatting
                return content.replace(/\n/g, '<br>');
            }

            var formattedReplyContent = formatReplyContent(replyContent, characterLimit);

            // Show reply context and fill the form
            $('#reply-context').html(
                '<strong>Replying to:</strong> ' + 
                '<span class="reply-info"><strong>' + replyUsername + ':</strong> <pre> ' + formattedReplyContent + '</pre> </span>'
            );
            $('#parent_id').val(replyId);
            $('#action_type').val('reply');  // Set action type to 'reply'
            $('textarea[name="reply_content"]').attr('placeholder', 'Write your reply...');
            $('#reply_id').val(0);  // Reset the reply_id for new replies

            // Show the reply form
            $('#reply-context-container').show();
            $('.reply-form').show();
        });

        // Handle reply context cancel
        $('#reply-context-cancel').on('click', function() {
            $('#reply-context').html('Replying to:');
            $('#parent_id').val('NULL');
            $('textarea[name="reply_content"]').attr('placeholder', 'Add comment...');
            $('#action_type').val('reply');  // Reset action type
            $('#reply_id').val(0);  // Reset the reply_id

            // Hide the reply form
            $('#reply-context-container').hide();
        });

        $(document).on('click', '.delete-button', function() {
            var formId = $(this).data('form-id');
            confirmDeleteModal(formId, 'Confirm Deletion', 'Are you sure you want to delete this post?', 'Delete');
        });


        $(window).on('load', function() {
            <?php if ($successMessage): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 4500);
            <?php endif; ?>
        });
    </script>
</body>
</html>
