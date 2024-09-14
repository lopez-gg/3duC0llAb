<?php
require_once __DIR__ . '/../../config/db_config.php';

// Get parameters
$grade = $_GET['grade'] ?? '';
$searchKeyword = $_GET['search'] ?? '';
$sortOrder = $_GET['order'] ?? 'desc';
$page = (int)($_GET['page'] ?? 1);
$itemsPerPage = 10; // Set a default value for items per page

// Build the SQL query
$sql = "
    SELECT * 
    FROM archived_tasks
    WHERE title LIKE :searchKeyword 
      AND grade LIKE :grade
    ORDER BY created_at $sortOrder
    LIMIT :offset, :limit
";

// Prepare statement
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':searchKeyword', "%$searchKeyword%", PDO::PARAM_STR);
$stmt->bindValue(':grade', "%$grade%", PDO::PARAM_STR);
$stmt->bindValue(':offset', ($page - 1) * $itemsPerPage, PDO::PARAM_INT);
$stmt->bindValue(':limit', $itemsPerPage, PDO::PARAM_INT);

// Execute the query
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM archived_tasks
    WHERE title LIKE :searchKeyword 
      AND grade LIKE :grade
");
$totalStmt->bindValue(':searchKeyword', "%$searchKeyword%", PDO::PARAM_STR);
$totalStmt->bindValue(':grade', "%$grade%", PDO::PARAM_STR);
$totalStmt->execute();
$totalCount = $totalStmt->fetchColumn();

$totalPages = ceil($totalCount / $itemsPerPage);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'tasks' => $tasks,
    'totalPages' => $totalPages,
]);
?>
