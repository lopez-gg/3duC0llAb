<?php
// delete_reply.php

require_once __DIR__ . '/../config/db_config.php'; // Database config
require_once __DIR__ . '/../config/session_config.php'; // Include session config

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// Get the reply ID from the query string
$reply_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($reply_id <= 0) {
    echo "Invalid reply ID.";
    exit;
}

try {
    // Delete the reply from the database
    $query = "DELETE FROM forum_replies WHERE id = :reply_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':reply_id', $reply_id, PDO::PARAM_INT);
    $stmt->execute();

    // Redirect back to the post view
    header('Location: ../../public/post_view.php?id=' . $_GET['post_id']);
    exit;
} catch (PDOException $e) {
    log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
    echo "An error occurred.";
    exit;
}
?>
