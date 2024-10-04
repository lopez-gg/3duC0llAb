<?php
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 
require_once __DIR__ . '/../../src/processes/check_new_messages.php'; 

check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Check if ID is set in the query string
if (!isset($_GET['id'])) {
    // Redirect or handle the error
    echo 'id is not in the query string';
    // header("Location: faculty.php");
    exit;
}

// Fetch faculty member details
$facultyId = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $facultyId]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    // Redirect if faculty member not found
    $_SESSION['success_message'] = "Faculty member doesn't exist.";
    header("Location: faculty.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update faculty member details
    $username = $_POST['username'] ?? $faculty['username'];
    $firstname = $_POST['firstname'] ?? $faculty['firstname'];
    $lastname = $_POST['lastname'] ?? $faculty['lastname'];
    $gradeLevel = $_POST['gradeLevel'] ?? $faculty['gradeLevel'];
    $section = $_POST['section'] ?? $faculty['section'];
    $status = $_POST['status'] ?? $faculty['status'];

    $stmt = $pdo->prepare("
        UPDATE users 
        SET username = :username, firstname = :firstname, lastname = :lastname, 
            gradeLevel = :gradeLevel, section = :section, status = :status 
        WHERE id = :id
    ");
    
    $stmt->execute([
        ':username' => $username,
        ':firstname' => $firstname,
        ':lastname' => $lastname,
        ':gradeLevel' => $gradeLevel,
        ':section' => $section,
        ':status' => $status,
        ':id' => $facultyId
    ]);

    $_SESSION['success_message'] = 'Faculty member updated successfully.';
    header("Location: faculty.php");
    exit;
}

include '../display_mod.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Faculty Member</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/a/h-e-gen.css">
    <link rel="stylesheet" href="../../src/css/message.css">
</head>
<body>

    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
            <div class="container mt-4">
                <h2>Edit Faculty Member</h2>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($faculty['username']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="firstname" class="form-label">First Name</label>
                        <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo htmlspecialchars($faculty['firstname']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="lastname" class="form-label">Last Name</label>
                        <input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo htmlspecialchars($faculty['lastname']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="gradeLevel" class="form-label">Grade Level</label>
                        <input type="text" id="gradeLevel" name="gradeLevel" class="form-control" value="<?php echo htmlspecialchars($faculty['gradeLevel']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="section" class="form-label">Section</label>
                        <input type="text" id="section" name="section" class="form-control" value="<?php echo htmlspecialchars($faculty['section']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="active" <?php echo $faculty['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $faculty['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="deactivated" <?php echo $faculty['status'] == 'deactivated' ? 'selected' : ''; ?>>Deactivated</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Faculty</button>
                    <button type="button" class="btn btn-secondary" 
                            onclick="openDiscardChangesModal()">Cancel
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script src='../../src/js/notification.js'></script>
    <script src='../../src/js/toggleSidebar.js'></script>
    <script src='../../src/js/datetime.js'></script>
    <script src='../../src/js/message.js'></script>
    <script>
        function openDiscardChangesModal() {
            $('#discardChangesModal').modal('show');
        }

        $('#confirmDiscardButton').on('click', function() {
            window.location.href = 'faculty.php'; 
        });
    </script>
</body>
</html>
