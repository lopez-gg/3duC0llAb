<?php

require_once __DIR__ . '/../../config/db_config.php'; 
require_once __DIR__ . '/../../config/config.php';    
require_once __DIR__ . '/../../config/session_config.php';


function fetch_space_tasks($userId, $grade = '', $order = 'desc', $progress = '', $search = '', $page = 1, $itemsPerPage = 10) {
    global $pdo;

    // Set up pagination
    $offset = ($page - 1) * $itemsPerPage;

    try {
        // Prepare the SQL query
        $query = "
        SELECT t.id, t.title, t.description, t.taskType, t.tag, t.grade, t.progress, t.due_date, t.due_time,
               u_assigned.username AS assigned_username,
               u_by.username AS assigned_by_username
        FROM tasks t
        LEFT JOIN users u_assigned ON t.assignedTo = u_assigned.id
        LEFT JOIN users u_by ON t.assignedBy = u_by.id
        WHERE (t.assignedTo = :userId OR t.assignedBy = :userId OR (t.assignedBy = :userId AND t.assignedTo = :userId)) 
          AND t.progress != 'completed'
          AND t.grade = :grade ";
          
        if (!empty($progress)) {
            $query .= " AND t.progress = :progress";
        }

        if (!empty($search)) {
            $query .= " AND (t.title LIKE :search OR t.description LIKE :search)";
        }
        

        // Add sorting and pagination
        $query .= " ORDER BY t.due_date " . ($order === 'asc' ? 'ASC' : 'DESC') . " LIMIT :offset, :itemsPerPage";

        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':grade', $grade, PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':itemsPerPage', $itemsPerPage, PDO::PARAM_INT);

        if (!empty($progress)) {
            $stmt->bindValue(':progress', $progress);
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
            FROM tasks t
            WHERE (t.assignedTo = :userId OR t.assignedBy = :userId OR (t.assignedBy = :userId AND t.assignedTo = :userId)) 
            AND t.grade = :grade
        ";

        $countStmt = $pdo->prepare($countQuery);
        $countStmt->bindValue(':userId', $userId, PDO::PARAM_INT);
        $countStmt->bindValue(':grade', $grade, PDO::PARAM_STR);
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
