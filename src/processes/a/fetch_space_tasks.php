<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Read JSON input or GET parameters
$input = json_decode(file_get_contents('php://input'), true);
$grade = isset($input['grade']) ? $input['grade'] : (isset($_GET['grade']) ? $_GET['grade'] : null);
$progress = isset($input['progress']) ? $input['progress'] : (isset($_GET['progress']) ? $_GET['progress'] : '');
$order = isset($input['order']) ? ($input['order'] === 'asc' ? 'ASC' : 'DESC') : (isset($_GET['order']) ? ($_GET['order'] === 'asc' ? 'ASC' : 'DESC') : 'DESC');
$page = isset($input['page']) ? (int)$input['page'] : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
$itemsPerPage = 3; // You can adjust the number of items per page
$offset = ($page - 1) * $itemsPerPage;

// Check if grade is specified
if ($grade) {
    try {
        // Build the SQL query to fetch tasks
        $query = "
            SELECT t.id, t.title, t.description, t.taskType, t.tag, t.grade, t.progress, t.status, t.created_at, t.due_date, 
                   u.username AS assigned_username
            FROM tasks t
            LEFT JOIN users u ON t.assignedTo = u.id
            WHERE t.grade = :grade 
              AND t.progress != 'completed'"; // Only active tasks

        // Apply progress filter if specified
        if (!empty($progress)) {
            $query .= " AND t.progress = :progress";
        }

        // Add ordering and pagination
        $query .= " ORDER BY t.due_date $order LIMIT :offset, :itemsPerPage";

        // Prepare the query
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);

        if (!empty($progress)) {
            $stmt->bindParam(':progress', $progress, PDO::PARAM_STR);
        }

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the tasks
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Count total tasks for pagination
        $countQuery = "
            SELECT COUNT(*) 
            FROM tasks 
            WHERE grade = :grade 
              AND progress != 'completed'";
              
        if (!empty($progress)) {
            $countQuery .= " AND progress = :progress";
        }

        $countStmt = $pdo->prepare($countQuery);
        $countStmt->bindParam(':grade', $grade, PDO::PARAM_STR);
        
        if (!empty($progress)) {
            $countStmt->bindParam(':progress', $progress, PDO::PARAM_STR);
        }

        $countStmt->execute();
        $totalItems = $countStmt->fetchColumn();
        $totalPages = ceil($totalItems / $itemsPerPage);

        // Prepare the response data
        $response = [
            'tasks' => $tasks,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
        ];

        // Output the JSON response
        header('Content-Type: application/json');
        echo json_encode($response);

    } catch (PDOException $e) {
        // Log the error and return a JSON error message
        log_error('Error fetching tasks: ' . $e->getMessage(), 'db_errors.txt');
        echo json_encode(['error' => 'Failed to fetch tasks']);
    }
} else {
    echo json_encode(['error' => 'Grade not specified']);
}
?>
