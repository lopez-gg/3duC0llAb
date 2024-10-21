<?php
// post_view.php

require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

check_access('ADMIN');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
} else {
    $grade = $_SESSION['grade'];

    if (is_numeric($grade) && $grade >= 1 && $grade <= 6) {
        $grade = $grade;
        $gradetodisplay = 'Grade ' . intval($grade);
    } elseif (strtolower($grade) === 'sned' || strtolower($grade) === 'kinder' || strtolower($grade) === 'general' ) {
        $grade = $grade;
        $gradetodisplay = strtoupper($grade);
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
$currentDateTime = date('l, d/m/Y h:i:s A'); 

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
    $repliesQuery = "SELECT fr.id, fr.reply_content, fr.created_at, u.username, fr.parent_id, fr.user_id, fr.deleted
        FROM forum_replies fr
        JOIN users u ON fr.user_id = u.id
        WHERE fr.post_id = :post_id
        ORDER BY fr.created_at DESC";

    $repliesStmt = $pdo->prepare($repliesQuery);
    $repliesStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $repliesStmt->execute();
    $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);

    function displayReplies($replies, $post_id, $grade, $csrf_token, $parent_id = null, $level = 0) {
        $html = '';
        foreach ($replies as $reply) {
            if ($reply['parent_id'] == $parent_id) {
                // Apply a class to deleted replies for styling
                $html .= '<li class="list-group-item reply-item mb-3 ' . ($reply['deleted'] ? 'deleted' : '') . '" style="margin-left: ' . ($level * 20) . 'px;" data-reply-id="' . $reply['id'] . '">';
                
                $html .= '<div class="reply-header">';
        
                // Check if the reply is deleted
                if ($reply['deleted']) {
                    // Display the "This reply has been deleted" message
                    $html .= '<div><p><strong>' . htmlspecialchars($reply['username']) . '</strong> <small class="text-muted">' . $reply['created_at'] . '</small></p></div>';
                    $html .= '<p><em>This reply has been deleted.</em></p>';
                } else {
                    // Display normal reply content if not deleted
                    $html .= '<div><p><strong>' . htmlspecialchars($reply['username']) . '</strong> <small class="text-muted">' . $reply['created_at'] . '</small></p></div>';
                    $html .= '<p>' . nl2br(htmlspecialchars($reply['reply_content'])) . '</p>';
        
                    // Show edit and delete buttons only if the reply belongs to the current user and is not deleted
                    if ($reply['user_id'] == $_SESSION['user_id']) {
                        $html .= '<div class="reply-actions">';
                        $html .= '<button class="btn btn-warning btn-sm edit-button" data-reply-id="' . $reply['id'] . '" data-reply-content="' . htmlspecialchars($reply['reply_content']) . '">
                                    <i class="bi bi-pencil-square"></i>
                                  </button> ';
                        $html .= '<form id="delete_reply_' . $reply['id'] . '" action="../../src/processes/a/delete_reply.php" method="post" style="display:inline;">
                                    <input type="hidden" name="reply_id" value="' . $reply['id'] . '">
                                    <input type="hidden" name="post_id" value="' . $post_id . '">
                                    <input type="hidden" name="grade" value="' . $grade . '">
                                    <input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">
                                    <button type="button" class="btn btn-danger btn-sm delete-button" data-form-id="delete_reply_' . $reply['id'] . '" data-reply-id="' . $reply['id'] . '">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>';
                        $html .= '</div>';
                    }
                }
        
                $html .= '</div>'; // Close reply-header div
        
                // Footer for positioning the reply button (only show if the reply is not deleted)
                if (!$reply['deleted']) {
                    $html .= '<div class="reply-footer">';
                    $html .= '<button class="btn btn-link reply-button" data-reply-id="' . $reply['id'] . '" data-reply-username="' . htmlspecialchars($reply['username']) . '" data-reply-content="' . htmlspecialchars($reply['reply_content']) . '">
                                <i class="bi bi-reply"></i>
                              </button>';
                    $html .= '</div>'; // Close reply-footer div
                }
        
                // Recursively display sub-replies
                $html .= displayReplies($replies, $post_id, $grade, $csrf_token, $reply['id'], $level + 1);
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
    <link rel="stylesheet" href="../../src/css/message.css">

</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
            
            <h2 class="mb-4"> <?php echo strtoupper(htmlspecialchars($gradetodisplay)); ?> Forum > View post</h2>

            <div class="container mt-5">
        
                <div class="post-card post-container">
                    <div class="post-header">
                        <div class="post-title">
                            <?= htmlspecialchars($post['title']) ?>
                        </div>
                        <div>
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
                            <?= displayReplies($replies, $post_id, $grade, $csrf_token) ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-replies">No replies yet.</p>
                    <?php endif; ?>
                </div>

                <!-- Fixed Reply Form -->
                <div class="reply-form reply-form-container">
                    <!-- Hidden container for reply context -->
                    <div class="reply-context-container" id="reply-context-container">
                        <p id="reply-context">Replying to:</p>
                        <span id="reply-context-cancel" class="reply-context-cancel">Cancel</span>
                    </div>

                    <form id="replyForm" action="../../src/processes/a/submit_reply.php" method="post">

                            <!-- Container holding both input and button -->
                        <div class="input-group">
                            <div class="input-container">
                                <textarea class="form-control" name="reply_content" rows="1" placeholder="Add a comment..." required></textarea>
                            </div>
                        
                            <div class="button-container">
                                <button type="submit" class="btn btn-primary" >Submit</button>
                            </div>

                        </div>
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="post_id" value="<?= $post_id ?>">
                            <input type="hidden" name="grade" value="<?= $grade ?>">
                            <input type="hidden" name="parent_id" id="parent_id" value="NULL">
                            <input type="hidden" name="action_type" id="action_type" value="reply">
                            <input type="hidden" name="reply_id" id="reply_id" value="0">
                    </form>
                </div>
                
            </div>               
        </div>                
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../src/js/verify.js"></script>
    <script src="../../src/js/datetime.js"></script>
    <script src="../../src/js/toggleSidebar.js"></script>
    <script src='../../src/js/message.js'></script>
    
    <script>
        
        document.addEventListener('DOMContentLoaded', () => {
            $('#reply-context-container').hide();

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

        $(document).on('click', '.edit-button', function() {
            console.log('Edit button clicked');
            var replyId = $(this).data('reply-id');
            var replyContent = $(this).data('reply-content');

            // Log the values
            console.log('Reply ID:', replyId);
            console.log('Reply Content:', replyContent);

            // Show reply context (you may want to indicate it's in "edit mode" visually)
            $('#reply-context').html('<strong>Editing reply:</strong>');

            // Set the reply content in the textarea for editing
            $('textarea[name="reply_content"]').val(replyContent);

            // Change the action type to "edit" and set the reply ID to the one being edited
            $('#action_type').val('edit');
            $('#reply_id').val(replyId);

            // Change the placeholder to indicate editing mode
            $('textarea[name="reply_content"]').attr('placeholder', 'Edit your reply...');

            // Show the reply form if it's hidden
            $('#reply-context-container').show();
            $('.reply-form').show();
        });

        // Reset the form when canceling the reply context or after submission
        $('#reply-context-cancel').on('click', function() {
            // Reset everything back to new reply mode
            $('#reply-context').html('Replying to:');
            $('#parent_id').val('NULL');
            $('textarea[name="reply_content"]').val('');
            $('textarea[name="reply_content"]').attr('placeholder', 'Add comment...');
            $('#action_type').val('reply');  // Reset action type
            $('#reply_id').val(0);  // Reset reply_id

            // Hide the reply form
            $('#reply-context-container').hide();
        });


        $(document).on('click', '.delete-button', function() {
            var formId = $(this).data('form-id');
            confirmDeleteModal(formId, 'Confirm Deletion', 'Are you sure you want to delete this post?', 'Delete');
        });

        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.querySelector('textarea[name="reply_content"]');

            // Function to auto-resize the textarea
            function autoResize() {
                const maxLines = 5;
                const lineHeight = parseInt(window.getComputedStyle(textarea).lineHeight, 10); // Get the line height
                const maxHeight = lineHeight * maxLines; // Calculate max height based on the max number of lines
                
                textarea.style.height = 'auto'; // Reset height
                textarea.style.height = Math.min(textarea.scrollHeight, maxHeight) + 'px'; // Set new height, capped at maxHeight
            }

            // Attach the input event listener to auto-resize the textarea as the user types
            textarea.addEventListener('input', autoResize);

            // Initialize auto-resize in case there is pre-filled content
            autoResize();
        });



        $(window).on('load', function() {
            <?php if ($successMessage): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 3000);
            <?php endif; ?>
        });
    </script>
</body>
</html>
