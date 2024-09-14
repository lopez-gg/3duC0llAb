<?php
require_once __DIR__ . '/../../config/db_config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = isset($_POST['grade']) ? trim($_POST['grade']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $user_id = $_SESSION['user_id'];

    if ($grade && $title && $content) {
        try {
            $query = "
                INSERT INTO forum_posts (grade, title, content, user_id) 
                VALUES (:grade, :title, :content, :user_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':grade', $grade);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            echo json_encode(['success' => 'Post added successfully']);
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Failed to add post']);
        }
    } else {
        echo json_encode(['error' => 'Invalid input']);
    }
}
?>
