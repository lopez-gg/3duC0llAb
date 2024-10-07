<?php
require_once __DIR__ . '/../../../src/config/db_config.php'; 
require_once __DIR__ . '/../../../src/config/config.php'; 
require_once __DIR__ . '/../../../src/config/session_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('Invalid CSRF token');
    }
    $utype = $_SESSION['accType'];
    $post_id = intval($_POST['post_id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $grade = $_SESSION['grade'];

    if (empty($title) || empty($content) || empty($grade)) {
        die('Title, content, or grade cannot be empty.');
    }

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
        if ($grade != 'general'){
            if($utype === "ADMIN"){
                header("Location: ../../../public/admin/space_forum.php?grade=" . urlencode($grade));
            } else if ($utype === "USER"){
                header("Location: ../../../public/user/space_forum.php?grade=" . urlencode($grade));
            }
        }else{
            if($utype === "ADMIN"){
                header("Location: ../../../public/admin/general_forum.php?grade=" . urldecode($grade));
            } else if ($utype === "USER"){
                header("Location: ../../../public/user/general_forum.php?grade=" . urldecode($grade));
            }
        }
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        log_error('Forum post edition failed: ' . $e->getMessage(), 'error.log');
        die('Failed to update post.');
    }
}
?>
