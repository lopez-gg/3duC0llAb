<?php
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/session_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the year range from the POST request
    $yearRange = isset($_POST['yearRange']) ? $_POST['yearRange'] : null;
    $userID = $_SESSION['user_id'];

    if ($yearRange) {
        try {
            // Check if the year range already exists in the `sy` table
            $stmt = $pdo->prepare("SELECT sy_id FROM sy WHERE year_range = ?");
            $stmt->execute([$yearRange]);
            $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingRecord) {
                // echo "Year range already exists. Using the existing record.";
            } else {

                // If the record does not exist, insert the new year range
                $stmt = $pdo->prepare("INSERT INTO sy (year_range) VALUES (?)");
                $stmt->execute([$yearRange]);

                $stmt = $pdo->prepare("SELECT sy_id FROM sy WHERE year_range = ?");
                $stmt->execute([$yearRange]);
                $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
                $syId = $existingRecord['sy_id'];

                $activity_message = '[ADDED] New SY Calendar: "' . $yearRange . '"';
                add_activity_history($userID, $syId, $activity_message);
            }
        } catch (PDOException $e) {
            // Handle error
            log_error('Error saving year range: ' . $e->getMessage(), 'db_errors.txt');

            // echo "Failed to save year range.";
        }
    } else {
        echo "No year range provided.";
    }
} else {
    echo "Invalid request method.";
}
?>
