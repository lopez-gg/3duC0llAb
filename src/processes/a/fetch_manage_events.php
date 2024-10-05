<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read JSON input or GET parameters
$input = json_decode(file_get_contents('php://input'), true);
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$yearRange = isset($input['year_range']) ? $input['year_range'] : (isset($_GET['year_range']) ? $_GET['year_range'] : getCurrentYearRange($pdo));
$order = isset($input['order']) ? ($input['order'] === 'desc' ? 'DESC' : 'ASC') : (isset($_GET['order']) ? ($_GET['order'] === 'desc' ? 'DESC' : 'ASC') : 'ASC');
$page = isset($input['page']) ? (int)$input['page'] : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

// Determine the current year range if not specified
if (empty($yearRange)) {
    // Existing logic for determining the current year range...
}

// Build the base query
$query = "
    SELECT events.*, event_types.color 
    FROM events 
    JOIN event_types ON events.event_type = event_types.type
    WHERE events.year_range = :year_range";

// If there's a search term, add it to the query
if ($search) {
    $query .= " AND (title LIKE :searchTitle OR description LIKE :searchDescription)";
}

// Add ordering and pagination to the query
$query .= " ORDER BY event_date $order LIMIT :offset, :itemsPerPage";

$stmt = $pdo->prepare($query);
$params = [$yearRange];
$stmt->bindValue(':year_range', $yearRange, PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

if ($search) {
    $searchParam = "%" . $search . "%";
    $stmt->bindValue(':searchTitle', $searchParam, PDO::PARAM_STR); // For title
    $stmt->bindValue(':searchDescription', $searchParam, PDO::PARAM_STR); // For description
}

$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total events for pagination
$countQuery = "SELECT COUNT(*) FROM events WHERE year_range = :year_range";
if ($search) {
    $countQuery .= " AND (title LIKE :search OR description LIKE :search)";
}

$countStmt = $pdo->prepare($countQuery);
$countStmt->bindValue(':year_range', $yearRange, PDO::PARAM_STR);

if ($search) {
    $countStmt->bindValue(':search', $searchParam, PDO::PARAM_STR);
}

$countStmt->execute();
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Prepare data for JSON response
$response = [
    'events' => $events,
    'currentPage' => $page,
    'totalPages' => $totalPages,
    'itemsPerPage' => $itemsPerPage,
    'currentYearRange' => $yearRange
];

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
