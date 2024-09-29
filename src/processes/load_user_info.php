<?php
// load_user_info.php

include '../../config/database.php'; // Include your database connection

// Get the user ID from the POST request
$userId = $_POST['userId'];

if (!$userId) {
    echo json_encode(['error' => 'User ID is missing']);
    exit;
}

// Prepare and execute the query to get the user info
$sql = "SELECT username, gradeLevel, section FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'User not found']);
}

$stmt->close();
$conn->close();
?>
