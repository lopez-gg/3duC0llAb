<?php
require_once __DIR__ . '/../config/db_config.php'; // Database config

$grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';


try {
    // Prepare and execute SQL to fetch posts for the given grade, ordered by the most recent first
    $query = "SELECT * FROM forum_posts WHERE grade = :grade ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
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

} catch (PDOException $e) {
    // Log database errors
    log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
    throw new Exception('An error occurred while fetching posts from the database.');
}
