<?php
require_once __DIR__ . '/../../config/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the year range from the POST request
    $yearRange = isset($_POST['yearRange']) ? $_POST['yearRange'] : null;

    if ($yearRange) {
        try {
            // Insert the year range into the `sy` table
            $stmt = $pdo->prepare("INSERT INTO sy (year_range) VALUES (?)");
            $stmt->execute([$yearRange]);

            echo "Year range saved successfully!";
        } catch (PDOException $e) {
            // Handle error
            error_log('Error saving year range: ' . $e->getMessage());
            echo "Failed to save year range.";
        }
    } else {
        echo "No year range provided.";
    }
} else {
    echo "Invalid request method.";
}
?>
