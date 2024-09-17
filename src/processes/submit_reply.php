<?php
// submit_reply.php

require_once __DIR__ . '/../config/db_config.php'; // Database config
require_once __DIR__ . '/../config/session_config.php'; // Include session config

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
    $reply_content = isset($_POST['reply_content']) ? trim($_POST['reply_content']) : '';
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== 'NULL' ? (int)$_POST['parent_id'] : NULL;
    $acc_type = isset($_POST['at']) ? $_POST['at'] : 'N/A';

    if ($post_id <= 0 || empty($reply_content)) {
        echo "Invalid input.";
        exit;
    }

    try {
        // Insert the reply into the database
        $query = "INSERT INTO forum_replies (post_id, user_id, reply_content, parent_id, created_at)
                  VALUES (:post_id, :user_id, :reply_content, :parent_id, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':reply_content', $reply_content, PDO::PARAM_STR);
        $stmt->bindValue(':parent_id', $parent_id, PDO::PARAM_INT | PDO::PARAM_NULL); // Handle parent_id (can be null)
        $stmt->execute();

        // Redirect based on account type
        if ($acc_type === 'am') {
            header("Location: ../../public/admin/post_view.php?id=$post_id");
        } else if ($acc_type === 'ur') {
            header("Location: ../../public/user/post_view.php?id=$post_id");
        } else {
            echo 'Unknown account type.';
        }
    
        exit;
    } catch (PDOException $e) {
        log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
        echo "An error occurred.";
        exit;
    }
} else {
    echo "Invalid request.";
}
