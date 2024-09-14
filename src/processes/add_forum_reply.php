<?php
require_once __DIR__ . '/../../config/db_config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = isset($_POST['post_id']) ? trim($_POST['post_id']) : '';
    $reply_content = isset($_POST['reply_content']) ? trim($_POST['reply_content']) : '';
    $user_id = $_SESSION['user_id'];

    if ($post_id && $reply_content) {
        try {
            $query = "
                INSERT INTO forum_replies (post_id, reply_content, user_id) 
                VALUES (:post_id, :reply_content, :user_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':post_id', $post_id);
            $stmt->bindParam(':reply_content', $reply_content);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            echo json_encode(['success' => 'Reply added successfully']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Failed to add reply']);
        }
    } else {
        echo json_encode(['error' => 'Invalid input']);
    }
}
?>
