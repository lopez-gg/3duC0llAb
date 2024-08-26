<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';

// Fetch the most recent year ranges
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

// Now, use the determined $currentYearRange to fetch events
$itemsPerPage = 15;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1) * $itemsPerPage;
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

$query = "SELECT * FROM events WHERE year_range = :year_range ORDER BY event_date $order LIMIT :offset, :itemsPerPage";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':year_range', $currentYearRange, PDO::PARAM_STR);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
$stmt->execute();

$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total events for pagination
$countQuery = "SELECT COUNT(*) FROM events WHERE year_range = :year_range";
$countStmt = $pdo->prepare($countQuery);
$countStmt->bindParam(':year_range', $currentYearRange, PDO::PARAM_STR);
$countStmt->execute();
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $itemsPerPage);

// Prepare data for JSON response
$response = [
    'events' => $events,
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'itemsPerPage' => $itemsPerPage,
    'currentYearRange' => $currentYearRange // Send the year range as part of the response
];

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
