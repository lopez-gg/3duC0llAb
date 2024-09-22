<?php
require_once __DIR__ . '/../../config/db_config.php';

$gradeFilter = $_GET['grade'] ?? null;
$statusFilter = $_GET['status'] ?? null;  
$search = $_GET['search'] ?? null;

try {
    $query = "SELECT * FROM users WHERE accType = 'USER'";

    if ($statusFilter) {
        $query .= " AND status = :status";
    } else {
        $query .= " AND status != 'deactivated'";
    }
    
    if ($gradeFilter) {
        $query .= " AND gradeLevel = :grade";
    }
    
    if ($search) {
        $query .= " AND (firstname LIKE :search OR lastname LIKE :search)";
    }

    $stmt = $pdo->prepare($query);

    if ($statusFilter) {
        $stmt->bindParam(':status', $statusFilter);
    }
    if ($gradeFilter) {
        $stmt->bindParam(':grade', $gradeFilter);
    }
    if ($search) {
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm);
    }

    $stmt->execute();
    $faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($faculty as $index => $member) {
        echo "<tr>";
        echo "<td>" . ($index + 1) . "</td>";
        echo "<td>" . htmlspecialchars($member['username']) . "</td>";
        echo "<td>" . htmlspecialchars($member['firstname']) . "</td>";
        echo "<td>" . htmlspecialchars($member['lastname']) . "</td>";
        echo "<td>" . htmlspecialchars($member['gradeLevel']) . "</td>";
        echo "<td>" . htmlspecialchars($member['section']) . "</td>";
        echo "<td>" . htmlspecialchars($member['status']) . "</td>";
        echo "<td>
                <button class='btn btn-danger' onclick='confirmDeactivation(" . htmlspecialchars($member['id']) . ")'>Deactivate</button>
              </td>";
        echo "</tr>";
    }
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    die('Error fetching faculty members.');
}
