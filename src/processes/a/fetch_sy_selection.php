<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Retrieve year range and sorting order
$selectedYearRange = isset($input['year_range']) ? $input['year_range'] : '';
$order = isset($input['order']) && $input['order'] === 'desc' ? 'DESC' : 'ASC';

// Handle pagination
$itemsPerPage = 15;
$currentPage = isset($input['page']) ? (int)$input['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;

// Build the query
$query = "SELECT * FROM events WHERE 1=1"; // Basic query to start with

if ($selectedYearRange) {
    $query .= " AND year_range = :year_range";
}
$query .= " ORDER BY event_date $order LIMIT :offset, :itemsPerPage";

$stmt = $pdo->prepare($query);

if ($selectedYearRange) {
    $stmt->bindParam(':year_range', $selectedYearRange, PDO::PARAM_STR);
}
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total events for pagination
$countQuery = "SELECT COUNT(*) FROM events WHERE 1=1";

if ($selectedYearRange) {
    $countQuery .= " AND year_range = :year_range";
}

$countStmt = $pdo->prepare($countQuery);

if ($selectedYearRange) {
    $countStmt->bindParam(':year_range', $selectedYearRange, PDO::PARAM_STR);
}
$countStmt->execute();
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Prepare data for JSON response
$response = [
    'events' => $events,
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'itemsPerPage' => $itemsPerPage
];

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
