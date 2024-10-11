<?php
// fetch_availability.php

// Include config and database connection
require_once __DIR__ . '/../config/db_config.php';  // Contains the database connection
require_once __DIR__ . '/../config/session_config.php';  // Contains session setup

// Get faculty ID, year, and month from the request
$faculty_id = isset($_GET['faculty_id']) ? (int) $_GET['faculty_id'] : null;
$year = isset($_GET['year']) ? (int) $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int) $_GET['month'] : date('m');

if (!$faculty_id) {
    echo json_encode(['error' => 'No faculty ID provided']);
    exit;
}

try {
    // Fetch tasks for the faculty member
    $tasks_query = $pdo->prepare("SELECT task_date FROM tasks WHERE faculty_id = :faculty_id AND YEAR(task_date) = :year AND MONTH(task_date) = :month");
    $tasks_query->execute(['faculty_id' => $faculty_id, 'year' => $year, 'month' => $month]);
    $tasks = [];
    while ($row = $tasks_query->fetch(PDO::FETCH_ASSOC)) {
        $tasks[] = $row['task_date'];
    }

    // Fetch events for the faculty member
    $events_query = $pdo->prepare("SELECT event_date FROM events WHERE faculty_id = :faculty_id AND YEAR(event_date) = :year AND MONTH(event_date) = :month");
    $events_query->execute(['faculty_id' => $faculty_id, 'year' => $year, 'month' => $month]);
    $events = [];
    while ($row = $events_query->fetch(PDO::FETCH_ASSOC)) {
        $events[] = $row['event_date'];
    }

    // Fetch appointments for the faculty member
    $appointments_query = $pdo->prepare("SELECT appointment_date FROM appointments WHERE faculty_id = :faculty_id AND YEAR(appointment_date) = :year AND MONTH(appointment_date) = :month");
    $appointments_query->execute(['faculty_id' => $faculty_id, 'year' => $year, 'month' => $month]);
    $appointments = [];
    while ($row = $appointments_query->fetch(PDO::FETCH_ASSOC)) {
        $appointments[] = $row['appointment_date'];
    }

    // Return results as JSON
    echo json_encode([
        'tasks' => $tasks,
        'events' => $events,
        'appointments' => $appointments
    ]);

} catch (PDOException $e) {
    log_error('Failed to fetch availability: ' . $e->getMessage(), 'fetch_availability_errors.txt');
    echo json_encode(['error' => 'Failed to fetch availability']);
}
?>
