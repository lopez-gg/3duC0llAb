<?php
// tasks.php
require_once __DIR__ . '/../config/db_config.php'; // Include database configuration
require_once __DIR__ . '/../config/config.php'; // Include global configuration

function getTasks($userId, $filter = 'my_tasks') {
    global $pdo;

    // Define the SQL query based on the filter
    if ($filter === 'my_tasks') {
        $sql = "
            SELECT * FROM tasks
            WHERE assignedBy = :userId
            ORDER BY FIELD(tag, 'UI', 'UNI', 'NUI', 'NUNI'), due_date ASC
        ";
    } else {
        $sql = "
            SELECT * FROM tasks
            WHERE assignedTo = :userId
            ORDER BY FIELD(tag, 'UI', 'UNI', 'NUI', 'NUNI'), due_date ASC
        ";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['userId' => $userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTopTasks($userId) {
    $tasks = getTasks($userId, 'my_tasks');
    return array_slice($tasks, 0, 3); // Get top 3 tasks
}
?>
