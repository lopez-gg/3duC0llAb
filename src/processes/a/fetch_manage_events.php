<?php
require_once __DIR__ . '/../../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../../config/config.php'; // Include global configuration

$itemsPerPage = 15;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage);
$offset = ($currentPage - 1) * $itemsPerPage;

// Fetch filter parameters
$month = isset($_GET['month']) ? (int)$_GET['month'] : null;
$year = isset($_GET['year']) ? (int)$_GET['year'] : null;

try {
    // Build the conditions array
    $conditions = [];
    $params = [];

    if ($month) {
        $conditions[] = "MONTH(event_date) = :month";
        $params[':month'] = $month;
    }

    if ($year) {
        $conditions[] = "YEAR(event_date) = :year";
        $params[':year'] = $year;
    }
    // Create base SQL for counting total items
    $totalItemsQuery = "SELECT COUNT(*) FROM events";
    if (!empty($conditions)) {
        $totalItemsQuery .= " WHERE " . implode(' AND ', $conditions);
    }

    $totalItemsStmt = $pdo->prepare($totalItemsQuery);
    foreach ($params as $key => $value) {
        $totalItemsStmt->bindValue($key, $value);
    }
    $totalItemsStmt->execute();
    $totalItems = $totalItemsStmt->fetchColumn();
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Create base SQL for fetching events
    $sql = "SELECT id, title, description, event_date as start, end_date as end FROM events";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';
    $sql .= " ORDER BY event_date $order LIMIT :offset, :itemsPerPage";

    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'events' => $events,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'itemsPerPage' => $itemsPerPage
    ]);
} catch (Exception $e) {
    log_error('Error fetching events: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode(['error' => 'An error occurred while fetching events.']);
}

// Fetch event types from the database
try {
    $stmt = $pdo->query("SELECT type FROM event_types");
    $eventTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle the error (you can log it or display a message)
    log_error('Error fetching event types: ' . $e->getMessage(), '../../../logs/error.log');
    $eventTypes = []; // Set to an empty array in case of an error
}
?>
