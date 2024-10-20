<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to fetch events based on search, year range, and pagination
function fetch_events($search = '', $yearRange = '', $order = 'ASC', $page = 1, $itemsPerPage = 20) {
    global $pdo;
    $yearRange = trim($yearRange);
    // Set up pagination
    $offset = ($page - 1) * $itemsPerPage;

    // Determine the current year range if not specified
    if (empty($yearRange)) {
        $yearRange = getCurrentYearRange($pdo); // Assuming you have this function
    }

    try {
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
        $stmt->bindValue(':year_range', $yearRange, PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

        if ($search) {
            $searchParam = "%" . $search . "%";
            $stmt->bindValue(':searchTitle', $searchParam, PDO::PARAM_STR); 
            $stmt->bindValue(':searchDescription', $searchParam, PDO::PARAM_STR); 
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

        // Return the events and pagination details as an array
        return [
            'events' => $events,
            'totalEvents' => count($events),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
            'currentYearRange' => $yearRange
        ];

    } catch (PDOException $e) {
        // Log database errors
        log_error('Database error: ' . $e->getMessage(), 'db_errors.txt');
        return ['error' => 'An error occurred while fetching events from the database.'];
    }
}
