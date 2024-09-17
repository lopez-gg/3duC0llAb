<?php
require_once __DIR__ . '/../config/db_config.php'; // Database config

$grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50; // Default limit of posts
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Default page
$offset = ($page - 1) * $limit;

try {
    // Prepare SQL to fetch posts along with username and reply count
    $query = "
        SELECT fp.id, fp.title, fp.content, fp.created_at, u.username, 
               (SELECT COUNT(*) FROM forum_replies WHERE post_id = fp.id) AS reply_count 
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

    // Calculate total pages
    $totalPages = ceil($totalPosts / $limit);

    // Return data as JSON
    echo json_encode([
        'posts' => $posts,
        'totalPosts' => $totalPosts,
        'totalPages' => $totalPages
    ]);
} catch (PDOException $e) {
    // Log database errors
    log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['error' => 'An error occurred while fetching posts from the database.']);
}
