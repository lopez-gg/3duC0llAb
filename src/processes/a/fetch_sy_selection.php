<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure PDO connection is available
if (!$pdo) {
    die(json_encode(['error' => 'Database connection failed.']));
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$selectedYearRange = isset($input['year_range']) ? $input['year_range'] : '';

if ($selectedYearRange) {
    // Set up pagination variables
    $itemsPerPage = 15;
    $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Ensure page is at least 1
    $offset = ($currentPage - 1) * $itemsPerPage;
    $order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

    // Prepare SQL query
    $query = "SELECT * FROM events WHERE year_range = :year_range ORDER BY event_date $order LIMIT :offset, :itemsPerPage";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':year_range', $selectedYearRange, PDO::PARAM_STR);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();

    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total events for pagination
    $countQuery = "SELECT COUNT(*) FROM events WHERE year_range = :year_range";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->bindParam(':year_range', $selectedYearRange, PDO::PARAM_STR);
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
} else {
    // Return an error if no year range is provided
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No year range provided.']);
}
?>
