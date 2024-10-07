<?php
require_once __DIR__ . '/../../config/db_config.php'; // Database configuration file
require_once __DIR__ . '/../../config/config.php';    // Application-specific configurations

// Capture the GET parameters or JSON input
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';  // Default to 'desc' if 'order' isn't specified
$input = json_decode(file_get_contents('php://input'), true);  // Decode JSON input if available
$progress = isset($input['progress']) ? $input['progress'] : (isset($_GET['progress']) ? $_GET['progress'] : '');
$order = isset($input['order']) ? ($input['order'] === 'asc' ? 'ASC' : 'DESC') : (isset($_GET['order']) ? ($_GET['order'] === 'asc' ? 'ASC' : 'DESC') : 'DESC');

// Pagination setup: Capture the page number (default is page 1)
$page = isset($input['page']) ? (int)$input['page'] : (isset($_GET['page']) ? (int)$_GET['page'] : 1);
$itemsPerPage = 15;  
$offset = ($page - 1) * $itemsPerPage;  


    try {
        $query = "
        SELECT t.id, t.title, t.description, t.taskType, t.tag, t.grade, t.progress, t.created_at, t.due_date, t.due_time, 
               u_assigned.username AS assigned_username,
               u_by.username AS assigned_by_username
        FROM tasks t
        LEFT JOIN users u_assigned ON t.assignedTo = u_assigned.id
        LEFT JOIN users u_by ON t.assignedBy = u_by.id
        WHERE t.taskType = 'assigned' 
          AND t.assignedTo = :userId
          AND t.progress != 'completed'";
    

        // Add progress filter if specified
        if (!empty($progress)) {
            $query .= " AND t.progress = :progress";
        }

        // Add ordering and pagination
        $query .= " ORDER BY t.due_date $order LIMIT :offset, :itemsPerPage";

        // Prepare the query
        $stmt = $pdo->prepare($query); 
        $stmt->bindParam(':userId', $userId, PDO::PARAM_STR);

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

?>
