<?php
// fetch_post_details.php

require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/session_config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

// Get the post ID from the query string
$post_id = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$replies_per_page = 50;

if ($post_id <= 0 || $page <= 0) {
    echo json_encode(['error' => 'Invalid parameters.']);
    exit;
}

$offset = ($page - 1) * $replies_per_page;

try {
    // Fetch the post details
    $postQuery = "SELECT fp.id, fp.title, fp.content, fp.created_at, u.username
                  FROM forum_posts fp
                  JOIN users u ON fp.user_id = u.id
                  WHERE fp.id = :post_id";
    $postStmt = $pdo->prepare($postQuery);
    $postStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $postStmt->execute();
    $post = $postStmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo json_encode(['error' => 'Post not found.']);
        exit;
    }

    // Fetch replies with pagination
    $repliesQuery = "SELECT fr.id, fr.reply_content, fr.created_at, u.username, fr.user_id, fr.parent_id
                     FROM forum_replies fr
                     JOIN users u ON fr.user_id = u.id
                     WHERE fr.post_id = :post_id
                     ORDER BY fr.created_at ASC
                     LIMIT :limit OFFSET :offset";
    $repliesStmt = $pdo->prepare($repliesQuery);
    $repliesStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $repliesStmt->bindValue(':limit', $replies_per_page, PDO::PARAM_INT);
    $repliesStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $repliesStmt->execute();
    $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total number of replies for pagination
    $totalRepliesQuery = "SELECT COUNT(*) FROM forum_replies WHERE post_id = :post_id";
    $totalRepliesStmt = $pdo->prepare($totalRepliesQuery);
    $totalRepliesStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $totalRepliesStmt->execute();
    $totalReplies = $totalRepliesStmt->fetchColumn();
    $totalPages = ceil($totalReplies / $replies_per_page);

    // Return post and replies data as JSON
    echo json_encode([
        'post' => $post,
        'replies' => $replies,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
