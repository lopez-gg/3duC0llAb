<?php
require_once __DIR__ . '/../../src/config/db_config.php';

// Set default values
$grade = $_GET['grade'] ?? null;
$status = $_GET['status'] ?? null;

// Prepare your query based on the filters
$query = "SELECT * FROM users WHERE status != 'deactivated'";

$params = [];

// Add filtering conditions based on inputs
if ($grade) {
    $query .= " AND gradeLevel = :grade";
    $params[':grade'] = $grade;
}
if ($status) {
    $query .= " AND status = :status";
    $params[':status'] = $status;
}

// Use PDO to prepare and execute the statement
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$facultyMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate HTML rows
$output = '';
foreach ($facultyMembers as $index => $faculty) {
    $output .= '<tr>';
    $output .= '<td>' . ($index + 1) . '</td>';
    $output .= '<td>' . htmlspecialchars($faculty['username']) . '</td>';
    $output .= '<td>' . htmlspecialchars($faculty['firstname']) . '</td>';
    $output .= '<td>' . htmlspecialchars($faculty['lastname']) . '</td>';
    $output .= '<td>' . htmlspecialchars($faculty['gradeLevel']) . '</td>';
    $output .= '<td>' . htmlspecialchars($faculty['section']) . '</td>';
    $output .= '<td>' . htmlspecialchars($faculty['status']) . '</td>';
    $output .= '<td><!-- Actions --></td>'; // Add your actions here
    $output .= '</tr>';
}

echo $output;
?>
