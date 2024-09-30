<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read JSON input or GET parameters
$input = json_decode(file_get_contents('php://input'), true);
$yearRange = isset($input['year_range']) ? $input['year_range'] : (isset($_GET['year_range']) ? $_GET['year_range'] : '');
$order = isset($input['order']) ? ($input['order'] === 'desc' ? 'DESC' : 'ASC') : (isset($_GET['order']) ? ($_GET['order'] === 'desc' ? 'DESC' : 'ASC') : 'ASC');
$page = isset($input['page']) ? (int)$input['page'] : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
$itemsPerPage = 20;
$offset = ($page - 1) * $itemsPerPage;

// Determine the current year range if not specified
if (empty($yearRange)) {
    $query = "SELECT DISTINCT year_range FROM sy ORDER BY year_range DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $yearRanges = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $currentYear = date('Y');
    $currentYearRange = ''; // Default value
    foreach ($yearRanges as $range) {
        $parts = explode('-', $range['year_range']);
        if ($parts[0] == $currentYear) {
            $currentYearRange = $range['year_range'];
            break;
        }
    }

    // Check if all events in the current year range have passed
    $checkQuery = "SELECT COUNT(*) FROM events WHERE year_range = :year_range AND event_date >= NOW()";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->bindParam(':year_range', $currentYearRange, PDO::PARAM_STR);
    $checkStmt->execute();
    $activeEvents = $checkStmt->fetchColumn();

    if ($activeEvents == 0) {
        // Move to the next school year
        $nextYearRangeQuery = "SELECT year_range FROM sy WHERE year_range > :current_year_range ORDER BY year_range ASC LIMIT 1";
        $nextYearRangeStmt = $pdo->prepare($nextYearRangeQuery);
        $nextYearRangeStmt->bindParam(':current_year_range', $currentYearRange, PDO::PARAM_STR);
        $nextYearRangeStmt->execute();
        $nextYearRange = $nextYearRangeStmt->fetchColumn();

        if ($nextYearRange) {
            $currentYearRange = $nextYearRange;
        }
    }

    $yearRange = $currentYearRange;
}

    // Build the query to fetch events and their associated colors
    $query = "
        SELECT events.*, event_types.color 
        FROM events 
        JOIN event_types ON events.event_type = event_types.type
        WHERE events.year_range = :year_range 
        ORDER BY event_date $order 
        LIMIT :offset, :itemsPerPage";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':year_range', $yearRange, PDO::PARAM_STR);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total events for pagination
    $countQuery = "SELECT COUNT(*) FROM events WHERE year_range = :year_range";
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->bindParam(':year_range', $yearRange, PDO::PARAM_STR);
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
