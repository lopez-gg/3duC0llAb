<?php
require_once __DIR__ . '/../../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../../config/config.php'; // Include global configuration

$itemsPerPage = 15;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, $currentPage);
$offset = ($currentPage - 1) * $itemsPerPage;

try {
    // Fetch total number of items
    $totalItemsQuery = $pdo->query("SELECT COUNT(*) FROM events");
    $totalItems = $totalItemsQuery->fetchColumn();
    $totalPages = ceil($totalItems / $itemsPerPage);

    // Fetch paginated events
    $order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

    $stmt = $pdo->prepare("SELECT id, title, description, event_date as start, end_date as end FROM events ORDER BY event_date $order LIMIT :offset, :itemsPerPage");
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'events' => $events,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage
    ]);
} catch (Exception $e) {
    log_error('Error fetching events: ' . $e->getMessage(), 'db_errors.txt');
    echo json_encode([]);
}
?>
