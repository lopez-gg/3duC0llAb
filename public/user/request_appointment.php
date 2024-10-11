<?php
// request_appointment.php
require_once __DIR__ . '/../../src/config/db_config.php'; 
require_once __DIR__ . '/../../src/config/session_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../../public/login.php');
    exit;
}

$faculty = null;
if (isset($_GET['f_id'])) {
    $funame = $_GET['f_id'];

    // Fetch faculty details
    try {
        $query = "SELECT id, username, firstname, lastname FROM users WHERE username = :f_uname";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':f_uname', $funame, PDO::PARAM_STR);
        $stmt->execute();
        
        $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$faculty) {
            die('Faculty not found');
        }
    } catch (PDOException $e) {
        die('Database error: ' . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Appointment</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> 
</head>
<body>
    <div class="container">
        <h2>Request Appointment</h2>
        <form action="../../src/processes/process_appointment.php" method="POST">
            <input type="hidden" name="faculty_id" value="<?= $faculty['id'] ?? ''; ?>"> <!-- Use null coalescing operator -->

            <div class="mb-3">
                <label for="facultyName" class="form-label">Faculty Name</label>
                <input type="text" class="form-control" id="facultyName" name="faculty_name" 
                    value="<?php echo htmlspecialchars(($faculty['firstname'] ?? '') . ' ' . ($faculty['lastname'] ?? '')); ?>" 
                    readonly>
            </div>

            <div class="mb-3">
                <label for="facultyUsername" class="form-label">Faculty Username</label>
                <input type="text" class="form-control" id="facultyUsername" name="faculty_username" 
                    value="<?php echo htmlspecialchars($faculty['username'] ?? ''); ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="appointmentDate" class="form-label">Appointment Date</label>
                <input type="date" class="form-control" id="appointmentDate" name="appointment_date" required>
            </div>

            <div class="mb-3">
                <label for="appointmentTime" class="form-label">Appointment Time</label>
                <input type="time" class="form-control" id="appointmentTime" name="appointment_time" required>
            </div>

            <div class="mb-3">
                <label for="additionalNotes" class="form-label">Additional Notes</label>
                <textarea class="form-control" id="additionalNotes" name="additional_notes" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Submit Request</button>
        </form>
    </div>

    <script src="path/to/bootstrap.bundle.js"></script>
</body>
</html>
