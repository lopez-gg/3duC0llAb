
<?php

require_once __DIR__ . '/../../config/db_config.php'; 
require_once __DIR__ . '/../../config/config.php';    
require_once __DIR__ . '/../../config/session_config.php';


function fetch_del_tasks($userId, $order = 'desc', $progress = '', $search = '', $page = 1, $itemsPerPage = 10) {
    global $pdo;

    // Set up pagination
    $offset = ($page - 1) * $itemsPerPage;

    try {
        // Prepare the SQL query
        $query = "
        SELECT at.id, at.title, at.description, at.taskType, at.tag, at.grade, at.progress, at.due_date, at.due_time,
               u_assigned.username AS assigned_username,
               u_by.username AS assigned_by_username
        FROM archived_tasks at
        LEFT JOIN users u_assigned ON at.assignedTo = u_assigned.id
        LEFT JOIN users u_by ON at.assignedBy = u_by.id
        WHERE (at.assignedTo = :userId OR (at.assignedBy = :userId AND at.assignedTo = :userId))";
          
        if (!empty($progress)) {
            $query .= " AND t.progress = :progress";
        }

        if (!empty($taskType)) {
          $query .= " AND t.taskType = :taskType";
      }

        if (!empty($search)) {
            $query .= " AND (t.title LIKE :search OR t.description LIKE :search)";
        }
        

        // Add sorting and pagination
        $query .= " ORDER BY at.due_date " . ($order === 'asc' ? 'ASC' : 'DESC') . " LIMIT :offset, :itemsPerPage";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

        if (!empty($progress)) {
            $stmt->bindValue(':progress', $progress);
        }

        if (!empty($taskType)) {
          $query .= " AND t.taskType = :taskType";
      }

        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bindParam(':search', $searchParam);
        }

        $stmt->execute();
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total number of tasks for pagination
        $countQuery = "
            SELECT COUNT(*) as totalTasks 
            FROM archived_tasks at
            WHERE at.assignedBy = :userId AND at.assignedTo = :userId";

        $countStmt = $pdo->prepare($countQuery);
        $countStmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $countStmt->execute();
        $totalTasks = $countStmt->fetch(PDO::FETCH_ASSOC)['totalTasks'];
        $totalPages = ceil($totalTasks / $itemsPerPage);

        return [
            'tasks' => $tasks,
            'totalPages' => $totalPages
        ];
    } catch (PDOException $e) {
        return ['error' => 'Database error: ' . $e->getMessage()];
    }
}
