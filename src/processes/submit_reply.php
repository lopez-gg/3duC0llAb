<?php
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/login.php');
    exit;
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== 'NULL' ? (int)$_POST['parent_id'] : null;
$reply_id = isset($_POST['reply_id']) ? (int)$_POST['reply_id'] : 0;
$action_type = isset($_POST['action_type']) ? $_POST['action_type'] : 'reply';
$reply_content = isset($_POST['reply_content']) ? trim($_POST['reply_content']) : '';
$csrf_token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';

if ($_SESSION['csrf_token'] !== $csrf_token) {
    echo "CSRF token validation failed.";
    exit;
}

if (isset($_POST['grade'])) {
    $grade = $_POST['grade'];
}

if (empty($reply_content)) {
    echo "Reply content cannot be empty.";
    exit;
}

try {
    if ($action_type === 'edit' && $reply_id > 0) {
        // Edit existing reply
        $updateReplyQuery = "UPDATE forum_replies 
                             SET reply_content = :reply_content 
                             WHERE id = :reply_id AND user_id = :user_id";
        $stmt = $pdo->prepare($updateReplyQuery);
        $stmt->bindValue(':reply_content', $reply_content, PDO::PARAM_STR);
        $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        
        $_SESSION['success_message'] = 'Reply edited successfully.';
        header("Location: ../../public/admin/post_view.php?grade=$grade&id=$post_id&edited_reply=$reply_id");
        exit;
    } else {
        // Insert new reply
        $insertReplyQuery = "INSERT INTO forum_replies (post_id, parent_id, user_id, reply_content, created_at)
                             VALUES (:post_id, :parent_id, :user_id, :reply_content, NOW())";
        $stmt = $pdo->prepare($insertReplyQuery);
        $stmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindValue(':parent_id', $parent_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':reply_content', $reply_content, PDO::PARAM_STR);
        $stmt->execute();

        $new_reply_id = $pdo->lastInsertId();
        $_SESSION['success_message'] = 'Reply added successfully.';
        header("Location: ../../public/admin/post_view.php?grade=$grade&id=$post_id&new_reply=$new_reply_id");
        exit;
    }
} catch (PDOException $e) {
    log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
    echo "An error occurred.";
    exit;
}
?>
