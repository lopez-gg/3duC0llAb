<?php
// delete_reply.php

require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /public/login.php');
        exit;
    }
    $utype = $_SESSION['accType'];
    $reply_id = isset($_POST['reply_id']) ? (int)$_POST['reply_id'] : 0;
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $acc_type = isset($_POST['at']) ? (int)$_POST['at'] : 0;
    $grade = isset($_POST['grade']) ? $_POST['grade'] : null;

    if ($reply_id <= 0 || $post_id <= 0) {
        $_SESSION['success_title'] = 'Error';
        $_SESSION['success_message'] = 'An error occurred. Please try again.';
        header('Location: ../../../public/admin/post_view.php?grade=' . $grade . '&id=' . $post_id);
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
            $_SESSION['success_title'] = 'Unauthorized Access';
            $_SESSION['success_message'] = 'You are not the owner of this reply.';
            if($utype === 'ADMIN'){
                header('Location: ../../../public/admin/post_view.php?grade=' . $grade . '&id=' . $post_id);
                exit;
            }if($utype === 'USER'){
                header('Location: ../../../public/user/post_view.php?grade=' . $grade . '&id=' . $post_id);
            exit;
            }
        }

        // Mark the reply as deleted instead of actually deleting it
        $deleteQuery = "UPDATE forum_replies SET deleted = 1 WHERE id = :reply_id";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $deleteStmt->execute();

        $_SESSION['success_title'] = 'Reply Deleted';
        $_SESSION['success_message'] = 'Your reply has been deleted.';
        if($utype === 'ADMIN'){
            header('Location: ../../../public/admin/post_view.php?grade=' . $grade . '&id=' . $post_id);
            exit;
        }if($utype === 'USER'){
            header('Location: ../../../public/user/post_view.php?grade=' . $grade . '&id=' . $post_id);
        exit;
        }
        exit;

    } catch (PDOException $e) {
        log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
        echo "An error occurred.";
        exit;
    }
}
