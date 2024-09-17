<?php
// fetch_post_details.php

require_once __DIR__ . '/../config/db_config.php'; // Database config
require_once __DIR__ . '/../config/session_config.php'; // Include session config

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
}

// Get the post ID and page from the query string
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($post_id <= 0) {
    echo "Invalid post ID.";
    exit;
}

$repliesPerPage = 50; // Number of replies to show per page
$offset = ($page - 1) * $repliesPerPage;

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
        echo "Post not found.";
        exit;
    }

    // Fetch replies for the post
    $repliesQuery = "SELECT fr.id, fr.reply_content, fr.created_at, u.username, fr.parent_id
                     FROM forum_replies fr
                     JOIN users u ON fr.user_id = u.id
                     WHERE fr.post_id = :post_id
                     AND fr.parent_id IS NULL
                     ORDER BY fr.created_at DESC
                     LIMIT :offset, :limit";
    $repliesStmt = $pdo->prepare($repliesQuery);
    $repliesStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $repliesStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $repliesStmt->bindValue(':limit', $repliesPerPage, PDO::PARAM_INT);
    $repliesStmt->execute();
    $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the total number of replies for pagination
    $totalRepliesQuery = "SELECT COUNT(*) as total
                          FROM forum_replies
                          WHERE post_id = :post_id
                          AND parent_id IS NULL";
    $totalRepliesStmt = $pdo->prepare($totalRepliesQuery);
    $totalRepliesStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
    $totalRepliesStmt->execute();
    $totalReplies = $totalRepliesStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($totalReplies / $repliesPerPage);

    // Recursive function to display replies and nested replies
    function displayReplies($pdo, $post_id, $parent_id = null, $level = 0) {
        $html = '';
        $repliesQuery = "SELECT fr.id, fr.reply_content, fr.created_at, u.username, fr.parent_id
                         FROM forum_replies fr
                         JOIN users u ON fr.user_id = u.id
                         WHERE fr.post_id = :post_id
                         AND fr.parent_id = :parent_id
                         ORDER BY fr.created_at ASC";
        $repliesStmt = $pdo->prepare($repliesQuery);
        $repliesStmt->bindValue(':post_id', $post_id, PDO::PARAM_INT);
        $repliesStmt->bindValue(':parent_id', $parent_id, PDO::PARAM_INT);
        $repliesStmt->execute();
        $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($replies as $reply) {
            $html .= '<li class="list-group-item reply-item mb-3" style="margin-left: ' . ($level * 20) . 'px;" data-reply-id="' . $reply['id'] . '">';
            $html .= '<p><strong>' . htmlspecialchars($reply['username']) . '</strong> <small class="text-muted">' . $reply['created_at'] . '</small></p>';
            $html .= '<p>' . nl2br(htmlspecialchars($reply['reply_content'])) . '</p>';
            $html .= '<button class="btn btn-link reply-button" data-reply-id="' . $reply['id'] . '" data-reply-username="' . htmlspecialchars($reply['username']) . '">Reply</button>';
            $html .= '<button class="btn btn-link delete-reply-button" data-reply-id="' . $reply['id'] . '">Delete</button>';
            $html .= '<button class="btn btn-link toggle-replies" data-reply-id="' . $reply['id'] . '">Show Replies</button>';
            $html .= '<ul class="nested-replies" id="replies-' . $reply['id'] . '">' . displayReplies($pdo, $post_id, $reply['id'], $level + 1) . '</ul>';
            $html .= '</li>';
        }
        return $html;
    }

    // Get the replies HTML
    $repliesHtml = displayReplies($pdo, $post_id);

    // Return the data as JSON
    echo json_encode([
        'post' => $post,
        'replies' => $repliesHtml,
        'totalPages' => $totalPages
    ]);
    
} catch (PDOException $e) {
    log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
    echo "An error occurred.";
    exit;
}
?>
