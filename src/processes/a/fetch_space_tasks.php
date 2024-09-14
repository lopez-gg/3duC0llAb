<?php
require_once __DIR__ . '/../../config/db_config.php'; // Database configuration file
require_once __DIR__ . '/../../config/config.php';    // Application-specific configurations

// Capture the GET parameters or JSON input
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';  // Default to 'desc' if 'order' isn't specified
$input = json_decode(file_get_contents('php://input'), true);  // Decode JSON input if available
$grade = isset($input['grade']) ? $input['grade'] : (isset($_GET['grade']) ? $_GET['grade'] : null);
$progress = isset($input['progress']) ? $input['progress'] : (isset($_GET['progress']) ? $_GET['progress'] : '');
$order = isset($input['order']) ? ($input['order'] === 'asc' ? 'ASC' : 'DESC') : (isset($_GET['order']) ? ($_GET['order'] === 'asc' ? 'ASC' : 'DESC') : 'DESC');

// Pagination setup: Capture the page number (default is page 1)
$page = isset($input['page']) ? (int)$input['page'] : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
$itemsPerPage = 3;  // Set the number of tasks to display per page
$offset = ($page - 1) * $itemsPerPage;  // Calculate the offset for pagination

// Ensure the grade is provided
if ($grade) {
    try {
        // SQL query to fetch tasks, filtering by grade and progress
        $query = "
            SELECT t.id, t.title, t.description, t.taskType, t.tag, t.grade, t.progress, t.created_at, t.due_date, 
                   u.username AS assigned_username
            FROM tasks t
            LEFT JOIN users u ON t.assignedTo = u.id
            WHERE t.grade = :grade 
              AND t.progress != 'completed'";

        // Add progress filter if specified
        if (!empty($progress)) {
            $query .= " AND t.progress = :progress";
        }

        // Add ordering and pagination
        $query .= " ORDER BY t.due_date $order LIMIT :offset, :itemsPerPage";

        // Prepare the query
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':grade', $grade, PDO::PARAM_STR);  // Bind the grade parameter

        if (!empty($progress)) {
            $stmt->bindParam(':progress', $progress, PDO::PARAM_STR);  // Bind the progress parameter if provided
        }

        // Bind pagination parameters
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the resulting tasks
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Query to count the total number of tasks for pagination purposes
        $countQuery = "
            SELECT COUNT(*) 
            FROM tasks 
            WHERE grade = :grade 
              AND progress != 'completed'";
        
        if (!empty($progress)) {
            $countQuery .= " AND progress = :progress";
        }

        // Prepare and execute the count query
        $countStmt = $pdo->prepare($countQuery);
        $countStmt->bindParam(':grade', $grade, PDO::PARAM_STR);  // Bind the grade for count

        if (!empty($progress)) {
            $countStmt->bindParam(':progress', $progress, PDO::PARAM_STR);  // Bind progress if provided
        }

        $countStmt->execute();
        $totalItems = $countStmt->fetchColumn();  // Get the total number of tasks
        $totalPages = ceil($totalItems / $itemsPerPage);  // Calculate the total number of pages

        // Prepare the response data
        $response = [
            'tasks' => $tasks,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $itemsPerPage,
        ];

        // Return the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);

    } catch (PDOException $e) {
        // Log the error and return a JSON error response
        log_error('Error fetching tasks: ' . $e->getMessage(), 'db_errors.txt');
        echo json_encode(['error' => 'Failed to fetch tasks']);
    }
} else {
    // If grade isn't provided, return a JSON error message
    echo json_encode(['error' => 'Grade not specified']);
}
?>
