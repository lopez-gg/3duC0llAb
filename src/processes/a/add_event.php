<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';

$titles = $_POST['title'];
$descriptions = $_POST['description'];
$starts = $_POST['start'];
$ends = $_POST['end'];

try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, end_date) VALUES (?, ?, ?, ?)");
    for ($i = 0; $i < count($titles); $i++) {
        $stmt->execute([$titles[$i], $descriptions[$i], $starts[$i], $ends[$i]]);
    }
    $pdo->commit();
    header("Location: ../../public/admin/handle_events.php");
} catch (Exception $e) {
    $pdo->rollBack();
    log_error('Error adding bulk events: ' . $e->getMessage(), 'db_errors.txt');
    echo "Error adding bulk events.";
}
?>
