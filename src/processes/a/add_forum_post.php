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
    $grade = isset($_POST['grade']) ? trim($_POST['grade']) : $_SESSION['grade'];
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

            // Get the ID of the newly created post
            $postId = $pdo->lastInsertId();

            // Prepare notification messages
            if ($grade === 'general') {
                // For general posts, notify all users
                $notifContent = "A new topic has been posted in the General Forum: " . htmlspecialchars($title);
                $userId = null; // Leave user_id blank for general posts

                // Insert notification for all users
                $query = "INSERT INTO notifications (user_id, type, notif_content, event_id) VALUES (:user_id, 'forum_post', :notif_content, :event_id)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':user_id', $userId, PDO::PARAM_NULL);
                $stmt->bindParam(':notif_content', $notifContent, PDO::PARAM_STR);
                $stmt->bindParam(':event_id', $postId, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // For specific grade posts, fetch user IDs and notify them
                $query = "SELECT id FROM users WHERE gradeLevel = :grade";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);
                $stmt->execute();
                $userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $notifContent = "A new topic has been posted for Grade " . htmlspecialchars($grade) . ": " . htmlspecialchars($title);

                // Insert notification for each user in the grade
                foreach ($userIds as $userId) {
                    $query = "INSERT INTO notifications (user_id, type, notif_content, event_id) VALUES (:user_id, 'forum_post', :notif_content, :event_id)";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                    $stmt->bindParam(':notif_content', $notifContent, PDO::PARAM_STR);
                    $stmt->bindParam(':event_id', $postId, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

            if ($grade === 'KINDER' || $grade === 'general' || $grade === 'SNED'){
                $gradeval = strtoupper($grade) . ' Forum';
            }else {
                $gradeval = 'Grade ' . $grade . ' Forum';
            }
            $activity_message = '[ADDED] Post: "' . $title . '" in ' . $gradeval;
            add_activity_history($user_id, $postId, $activity_message);

            // Set a success message in the session and redirect to the forum page
            $_SESSION['success_title'] = 'Success';
            $_SESSION['success_message'] = 'Topic posted successfully!';
            if ($grade === 'general') {
                header('Location: ../../../public/admin/general_forum.php');
            } else {
                header('Location: ../../../public/admin/space_forum.php?grade=' . urlencode($grade));
            }
             exit;
        } catch (PDOException $e) {
            // Log the error and redirect with an error message
            log_error('Error adding events: ' . $e->getMessage(), 'db_errors.txt');
            $_SESSION['success_title'] = 'Failed';
            $_SESSION['success_message'] = 'Failed to post forum topic. Please try again.';
            if ($grade === 'general') {
                header('Location: ../../../public/admin/general_forum.php');
            } else {
                header('Location: ../../../public/admin/space_forum.php?grade=' . urlencode($grade));
            }
            exit;
        }
    } else {
        // Redirect with an error message if fields are missing
        $_SESSION['error_message'] = 'Please fill in all fields.';
        header("Location: ../../../public/admin/new_post.php");
        exit;
    }
}
?>

