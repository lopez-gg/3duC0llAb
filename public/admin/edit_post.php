<?php
require_once __DIR__ . '/../../src/config/session_config.php'; // For CSRF token and session management
require_once __DIR__ . '/../../src/config/db_config.php'; 

// Ensure user is logged in and authorized
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}else {
    $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
    $_SESSION['grade'] = $grade;

    if (is_numeric($grade) && $grade >= 1 && $grade <= 6) {
        $gradetodisplay = 'Grade ' . intval($grade);
    } elseif (strtolower($grade) === 'sned') {
        $gradetodisplay = strtoupper($grade);
    } else {
        $gradetodisplay = 'Unknown Grade'; 
    }
}

// Fetch the post to edit
$post_id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM forum_posts WHERE id = :id AND user_id = :user_id");
$stmt->execute(['id' => $post_id, 'user_id' => $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    die('Post not found or you are not authorized to edit this post.');
}

$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
</head>
<body>
    <h1>Edit Post</h1>
    <form method="post" action="../../src/processes/a/process_edit_post.php" style="display:inline;">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
        <input type="hidden" name="grade" value="<?= htmlspecialchars($grade); ?>">
                                       
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>
        <div>
            <label for="content">Content</label>
            <textarea name="content" id="content" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>
        <button type="submit">Update Post</button>
    </form>
</body>
</html>
