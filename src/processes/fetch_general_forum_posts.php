<?php

require_once __DIR__ . '/../config/db_config.php'; // Database config

function fetch_forum_posts($forum, $limit = 50, $page = 1) {
    global $pdo;

    // Set up pagination
    $offset = ($page - 1) * $limit;

    try {
        // Prepare SQL to fetch posts along with username and reply count
        $query = "
            SELECT fp.id, fp.title, fp.content, fp.created_at, u.username, 
                   (SELECT COUNT(*) FROM forum_replies WHERE post_id = fp.id) AS reply_count 
            FROM forum_posts fp
            JOIN users u ON fp.user_id = u.id
            WHERE fp.grade = :forum
            ORDER BY fp.created_at DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':forum', $forum, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch total count of posts for pagination
        $totalQuery = "SELECT COUNT(*) as total FROM forum_posts WHERE grade = :forum";
        $totalStmt = $pdo->prepare($totalQuery);
        $totalStmt->bindValue(':forum', $forum, PDO::PARAM_STR);
        $totalStmt->execute();
        
        $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
        $totalPosts = $totalResult['total'] ?? 0;

        // Calculate total pages
        $totalPages = ceil($totalPosts / $limit);

        // Return the posts and pagination details as an array
        return [
            'posts' => $posts,
            'totalPosts' => $totalPosts,
            'totalPages' => $totalPages
        ];

    } catch (PDOException $e) {
        // Log database errors
        log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
        return ['error' => 'An error occurred while fetching posts from the database.'];
    }
}

?>