<?php
require_once __DIR__ . '/../../config/db_config.php'; // Database config

$grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';

if ($grade) {
    try {
        // Fetch forum posts for the grade
        $query = "
            SELECT p.id, p.title, p.content, p.created_at, u.username 
            FROM forum_posts p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.grade = :grade
            ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':grade', $grade);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch replies for each post
        foreach ($posts as &$post) {
            $post_id = $post['id'];
            $replyQuery = "
                SELECT r.id, r.reply_content, r.created_at, u.username 
                FROM forum_replies r
                JOIN users u ON r.user_id = u.id 
                WHERE r.post_id = :post_id
                ORDER BY r.created_at ASC";
            $replyStmt = $pdo->prepare($replyQuery);
            $replyStmt->bindParam(':post_id', $post_id);
            $replyStmt->execute();
            $post['replies'] = $replyStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Return posts with replies as JSON
        header('Content-Type: application/json');
        echo json_encode($posts);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Failed to fetch forum posts.']);
    }
} else {
    echo json_encode(['error' => 'Grade not specified']);
}
?>
