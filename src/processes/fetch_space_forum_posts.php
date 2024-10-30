<?php

require_once __DIR__ . '/../config/db_config.php'; // Database config

$grade = 1;
$limit = 50;
$page = 1;

// Fetch posts
$result = fetch_forum_posts($grade, $limit, $page);
$offset = ($page - 1) * $limit;

// Check if there was an error
if (isset($result['error'])) {
    echo "<p>Error: " . htmlspecialchars($result['error']) . "</p>";
    exit;
}

// Extract posts from result
$posts = $result['posts'] ?? [];

// Check if there are any posts
$postsData = fetch_forum_posts($grade, $limit, $page);
if (isset($postsData['posts'])) {
    foreach ($postsData['posts'] as $post) {
        echo 'grade: ' . $post['grade']. '<br>';
        echo 'Title: ' . $post['title'] . '<br>';
        echo 'Content: ' . $post['content'] . '<br>';
        echo 'Reply Count: ' . $post['reply_count'] . '<br>';
        echo '<hr>'; // Add a separator for clarity
    }
} else {
    echo "Error: " . $postsData['error']; 
}


function fetch_forum_posts($grade, $limit = 50, $page = 1) {
    global $pdo;

    if (empty($grade)) {
        return ['error' => 'Grade cannot be empty.'];
    }

    // Set up pagination
    $offset = ($page - 1) * $limit;

    try {
        // Prepare SQL to fetch posts
        $query = "
            SELECT fp.id, fp.grade, fp.title, fp.content, fp.created_at, u.username, 
                   (SELECT COUNT(*) FROM forum_replies WHERE post_id = fp.id) AS reply_count, 
                   (SELECT COUNT(*) FROM forum_posts WHERE grade = :grade) AS fetched_posts 
            FROM forum_posts fp
            JOIN users u ON fp.user_id = u.id
            WHERE fp.grade = :grade
            ORDER BY fp.created_at DESC
            LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':grade', $grade, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        

        // Fetch total count of posts for pagination
        $totalQuery = "SELECT COUNT(*) as total FROM forum_posts WHERE grade = :grade";
        $totalStmt = $pdo->prepare($totalQuery);
        $totalStmt->bindValue(':grade', $grade, PDO::PARAM_STR);
        $totalStmt->execute();
        
        $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
        $totalPosts = $totalResult['total'] ?? 0;

        // Debugging output
        echo "Limit: $limit, Offset: $offset, Total Posts: $totalPosts<br>"; // Debugging total posts count

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
