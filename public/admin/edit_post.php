<?php
require_once __DIR__ . '/../../src/config/session_config.php'; 
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
} else {
    $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
    $_SESSION['grade'] = $grade;

    if (is_numeric($grade) && $grade >= 1 && $grade <= 6) {
        $gradetodisplay = 'Grade ' . intval($grade);
    } elseif (strtolower($grade) === 'sned') {
        $gradetodisplay = strtoupper($grade);
    } elseif (strtolower($grade === 'general')) {
        $gradetodisplay = 'PSCS General'; 
    } else {
        $gradetodisplay = 'Unknown grade';
    }
}

$currentDateTime = date('l, d/m/Y h:i:s A'); 
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
        
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/a/h-e-gen.css">
    <link rel="stylesheet" href="../../src/css/message.css">

</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>
    <div class="content" id="content">
        <h2> <?php echo $gradetodisplay?> Forum > Edit Post</h2>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form method="post" action="../../src/processes/a/process_edit_post.php" class="border p-4 rounded shadow-sm bg-light">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="grade" value="<?= htmlspecialchars($grade); ?>">

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea name="content" id="content" class="form-control" rows="10" required><?= htmlspecialchars($post['content']) ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../src/js/toggleSidebar.js"></script>
    <script src="../../src/js/verify.js"></script>
    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/message.js'></script>
</body>
</html>
