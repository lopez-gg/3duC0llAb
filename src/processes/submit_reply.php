<?php
// submit_reply.php

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== 'NULL' ? (int)$_POST['parent_id'] : null;
$reply_content = isset($_POST['reply_content']) ? $_POST['reply_content'] : '';
$action_type = isset($_POST['action_type']) ? $_POST['action_type'] : '';
$reply_id = isset($_POST['reply_id']) ? (int)$_POST['reply_id'] : 0;

if ($post_id <= 0 || empty($reply_content)) {
    echo "Invalid request.";
    exit;
}

try {
    if ($action_type == 'edit' && $reply_id > 0) {
        // Fetch the reply to check if it belongs to the current user
        $replyQuery = "SELECT user_id FROM forum_replies WHERE id = :reply_id";
        $replyStmt = $pdo->prepare($replyQuery);
        $replyStmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $replyStmt->execute();
        $reply = $replyStmt->fetch(PDO::FETCH_ASSOC);

        if (!$reply || $reply['user_id'] != $_SESSION['user_id']) {
            echo "Unauthorized access.";
            exit;
        }

        // Update the reply
        $updateQuery = "UPDATE forum_replies SET reply_content = :reply_content WHERE id = :reply_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->bindValue(':reply_content', $reply_content, PDO::PARAM_STR);
        $updateStmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $updateStmt->execute();

        $_SESSION['success_title'] = 'Reply Updated';
        $_SESSION['success_message'] = 'Your reply has been updated successfully.';
        header('Location: ../../public/admin/post_view.php?id=' . $post_id . '&edited_reply=' . $reply_id);
    } else {
        // Insert a new reply
        $insertQuery = "INSERT INTO forum_replies (post_id, parent_id, user_id, reply_content) VALUES (:post_id, :parent_id, :user_id, :reply_content)";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $insertStmt->bindValue(':parent_id', $parent_id, PDO::PARAM_INT);
        $insertStmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $insertStmt->bindValue(':reply_content', $reply_content, PDO::PARAM_STR);
        $insertStmt->execute();

        $_SESSION['success_title'] = 'Reply Posted';
        $_SESSION['success_message'] = 'Your reply has been posted successfully.';
        header('Location: ../../public/admin/post_view.php?id=' . $post_id);
    }
    exit;

} catch (PDOException $e) {
    log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
    echo "An error occurred.";
    exit;
}
