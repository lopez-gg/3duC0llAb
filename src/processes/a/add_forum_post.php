<?php
// add_forum_post.php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
    $_SESSION['error_message'] = 'User not logged in';
    header("Location: ../../../public/admin/space_forum.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = isset($_POST['grade']) ? trim($_POST['grade']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $user_id = $_SESSION['user_id'];

    // Ensure all fields are filled
    if (!empty($grade) && !empty($title) && !empty($content)) {
        try {
            // Insert the new forum post into the database
            $query = "INSERT INTO forum_posts (grade, title, content, user_id) VALUES (:grade, :title, :content, :user_id)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Set a success message in the session and redirect to the forum page
            $_SESSION['success_title'] = 'Success';
            $_SESSION['success_message'] = 'Topic posted successfully!';
            header('Location: ../../../public/admin/space_forum.php?grade=' . urlencode($grade));
            exit;
        } catch (PDOException $e) {
            // Log the error and redirect with an error message
            error_log('Database Error: ' . $e->getMessage(), 3, 'db_errors.log');
            $_SESSION['success_title'] = 'Failed';
            $_SESSION['succes_message'] = 'Failed to post forum topic. Please try again.';
            header('Location: ../../../public/admin/space_forum.php?grade=' . urlencode($grade));
            exit;
        }
    } else {
        // Redirect with an error message if fields are missing
        $_SESSION['error_message'] = 'Please fill in all fields.';
        header("Location: ../../../public/admin/space_forum.php");
        exit;
    }
}
?>
