<?php
// Fetch year ranges and set the current year range
$query = "SELECT DISTINCT year_range FROM sy ORDER BY year_range DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$yearRanges = $stmt->fetchAll(PDO::FETCH_ASSOC);

$currentYear = date('Y');
$currentYearRange = ''; // Default value

foreach ($yearRanges as $range) {
    $parts = explode('-', $range['year_range']);
    if ($parts[0] == $currentYear) {
        $currentYearRange = $range['year_range'];
        break;
    }
}

// Fetch event types
$eventTypesQuery = "SELECT DISTINCT event_type as type FROM events";
$eventTypesStmt = $pdo->prepare($eventTypesQuery);
$eventTypesStmt->execute();
$eventTypes = $eventTypesStmt->fetchAll(PDO::FETCH_ASSOC);

// Return the data as an array
return [
    'yearRanges' => $yearRanges,
    'eventTypes' => $eventTypes,
    'currentYearRange' => $currentYearRange
];
?>
