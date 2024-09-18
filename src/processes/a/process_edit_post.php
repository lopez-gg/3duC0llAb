<?php
require_once __DIR__ . '/../../config/db_config.php'; 
require_once __DIR__ . '/../../config/config.php'; 
require_once __DIR__ . '/../../config/session_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    $post_id = intval($_POST['post_id']);
    $title = $_POST['title'];
    $content = $_POST['content'];
    $grade = $_POST['grade'];

    // Ensure title and content are not empty
    if (empty($title) || empty($content) || empty($grade)) {
        die('Title and content cannot be empty.');
    }

    // Update post in the database
    try {
        $pdo->beginTransaction();

        $sql = "UPDATE forum_posts SET title = :title, content = :content WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'title' => $title,
            'content' => $content,
            'id' => $post_id,
            'user_id' => $_SESSION['user_id']
        ]);

        $pdo->commit();
        $_SESSION['success_title'] = "Success!";
        $_SESSION['success_message'] = "Post updated successfully!";
        header("Location: ../../../public/admin/space_forum.php?grade=" . urlencode($grade));
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        log_error('Forum post edition failed: ' . $e->getMessage(), 'error.log');
        
    }
}
?>
