<?php
require_once __DIR__ . '/../../config/db_config.php'; // Database configuration
require_once __DIR__ . '/../../config/config.php'; // Application configurations

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Fetch tasks for management based on grade, progress, and pagination parameters.
 *
 * @param string $grade The grade level of the tasks to fetch.
 * @param string $progress The progress status to filter tasks (optional).
 * @param string $order The order of the results (default is 'DESC').
 * @param int $page The current page number for pagination (default is 1).
 * @param int $itemsPerPage The number of items per page (default is 15).
 * @param string $search The search query for task title or description (optional).
 * @return array An array containing tasks and pagination details.
 */
function fetch_manage_tasks($grade, $progress = '', $order = 'DESC', $page = 1, $itemsPerPage = 15, $search = '') {
    global $pdo;

    // Set up pagination
    $offset = ($page - 1) * $itemsPerPage;

    try {
        // Build the base query
        $query = "
        SELECT t.id, t.title, t.description, t.taskType, t.tag, t.grade, t.progress, 
               t.created_at, t.due_date, t.due_time, 
               u_assigned.username AS assigned_username,
               u_by.username AS assigned_by_username
        FROM tasks t
        LEFT JOIN users u_assigned ON t.assignedTo = u_assigned.id
        LEFT JOIN users u_by ON t.assignedBy = u_by.id
        WHERE t.grade = :grade 
          AND t.progress != 'completed'";

        // Add progress filter if provided
        if (!empty($progress)) {
            $query .= " AND t.progress = :progress";
        }

        // Add search functionality if a search query is provided
        if (!empty($search)) {
            $query .= " AND (t.title LIKE :search OR t.description LIKE :search)";
        }

        // Add ordering and limit for pagination
        $query .= " ORDER BY t.due_date $order LIMIT :offset, :itemsPerPage";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);

        if (!empty($progress)) {
            $stmt->bindParam(':progress', $progress, PDO::PARAM_STR);
        }

        if (!empty($search)) {
            $searchTerm = "%" . $search . "%";
            $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        }

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count total number of items for pagination
        $countQuery = "
            SELECT COUNT(*) 
            FROM tasks 
            WHERE grade = :grade 
              AND progress != 'completed'";

        // Add progress filter if provided
        if (!empty($progress)) {
            $countQuery .= " AND progress = :progress";
        }

        // Add search condition if provided
        if (!empty($search)) {
            $countQuery .= " AND (title LIKE :search OR description LIKE :search)";
        }

        $countStmt = $pdo->prepare($countQuery);
        $countStmt->bindParam(':grade', $grade, PDO::PARAM_STR);

        if (!empty($progress)) {
            $countStmt->bindParam(':progress', $progress, PDO::PARAM_STR);
        }

        if (!empty($search)) {
            $countStmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        }

        $countStmt->execute();
        $totalItems = $countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $itemsPerPage);

        // Return the tasks and pagination details as an array
        return [
            'tasks' => $tasks,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
        ];
        
    } catch (PDOException $e) {
        log_error('Error fetching tasks: ' . $e->getMessage(), 'db_errors.txt');
        return ['error' => 'Failed to fetch tasks'];
    }
}

?>
