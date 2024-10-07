<?php
// edit_reply.php

require_once __DIR__ . '/../src/config/db_config.php';
require_once __DIR__ . '/../src/config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

$utyp = $_SESSION['accType'];
$reply_id = isset($_POST['reply_id']) ? (int)$_POST['reply_id'] : 0;
$new_content = isset($_POST['reply_content']) ? $_POST['reply_content'] : '';

if ($reply_id <= 0 || empty($new_content)) {
    echo "Invalid request.";
    exit;
}

try {
    // Fetch the reply to check if it belongs to the current user
    $replyQuery = "SELECT user_id FROM forum_replies WHERE id = :reply_id";
    $replyStmt = $pdo->prepare($replyQuery);
    $replyStmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
    $replyStmt->execute();
    $reply = $replyStmt->fetch(PDO::FETCH_ASSOC);

    if (!$reply || $reply['user_id'] != $_SESSION['user_id']) {
        echo "Unauthorized access.";
        if($utype === 'ADMIN'){
            header('Location: ../../../public/admin/post_view.php?id=' . $_POST['post_id']);
            exit;
        }if($utype === 'USER'){
            header('Location: ../../../public/user/post_view.php?id=' . $_POST['post_id']);
        exit;
        }
    }

    // Update the reply content
    $updateQuery = "UPDATE forum_replies SET reply_content = :reply_content WHERE id = :reply_id";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->bindValue(':reply_content', $new_content, PDO::PARAM_STR);
    $updateStmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
    $updateStmt->execute();

    $_SESSION['success_title'] = 'Reply Updated';
    $_SESSION['success_message'] = 'Your reply has been updated successfully.';

    if($utype === 'ADMIN'){
        header('Location: ../../../public/admin/post_view.php?id=' . $_POST['post_id']);
        exit;
    }if($utype === 'USER'){
        header('Location: ../../../public/user/post_view.php?id=' . $_POST['post_id']);
    exit;
    }

} catch (PDOException $e) {
    log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
    echo "An error occurred.";
    exit;
}
