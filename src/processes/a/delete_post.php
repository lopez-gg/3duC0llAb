<?php
require_once __DIR__ . '/../../config/session_config.php';
require_once __DIR__ . '/../../config/db_config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }

    $post_id = intval($_POST['post_id']);
    $grade = isset($_POST['grade']) ? isset($_POST['grade']) : null ;

    // Delete post from the database
    try {
        $pdo->beginTransaction();

        $sql = "DELETE FROM forum_posts WHERE id = :id AND user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $post_id,
            'user_id' => $_SESSION['user_id']
        ]);

        $pdo->commit();
        $_SESSION['success_message'] = "Post deleted successfully!";
        header("Location: ../../../public/admin/space_forum.php?grade=" . urlencode($grade));
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to delete post: " . $e->getMessage();
    }
}
?>
