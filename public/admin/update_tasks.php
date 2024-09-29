<?php

require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';

check_access('ADMIN');

if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php'); 
    exit;
}else {
    $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
    $_SESSION['grade'] = $grade;

    if ($grade === 'sned'){
        $gradetodisplay = strtoupper($grade);
    } else {
        $gradetodisplay = 'Grade ' . $grade;
    }
}

date_default_timezone_set('Asia/Manila'); 
$currentDateTime = date('l, d/m/Y h:i:s A'); 

$grade = isset($_GET['grade']) ? $_GET['grade'] : null;
$id = isset($_GET['id']) ? $_GET['id'] : null;
$task = [
    'title' => '',
    'description' => '',
    'tag' => '',
    'grade' => '',
    'progress' => '',
    'due_date' => '',
    'due_time' => ''
];

if ($id) {
    $stmt = $pdo->prepare("SELECT t.*, u.username AS assigned_username 
                           FROM tasks t
                           LEFT JOIN users u ON t.assignedTo = u.id 
                           WHERE t.id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header('Location: space_home.php?grade=' . $grade);
    exit;
}

$tags = ['Normal', 'Urgent', 'Important', 'Urgent and Important'];
$progressStatuses = ['completed', 'in_progress', 'pending'];

// debug
// if ($id && $grade) {
//     echo "Task ID: " . htmlspecialchars($id) . "<br>";
//     echo "Grade: " . htmlspecialchars($grade) . "<br>";
// } else {
//     echo "Missing task ID or grade.";
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../src/css/gen.css">
    <link rel="stylesheet" href="../../src/css/form.css">
    <link rel="stylesheet" href="../../src/css/message.css">
</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
        <h2><?php echo $gradetodisplay?> > Edit Task</h2>

            <div class="form-container">
                <form id="taskForm" action="../../src/processes/a/process_update_task.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($task['id']); ?>">
                    
                    <div class="user-form-group">
                        <div class="form-group">
                            <label for="title">Title:</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <textarea class="form-control" name="description" value="None"><?php echo htmlspecialchars($task['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-group position-relative">
                            <label for="assignedToSearch">Assign To:</label>
                            <input type="text" class="form-control" id="assignedToSearch" autocomplete="off" 
                                placeholder="Search here..." value="<?= htmlspecialchars($task['assigned_username'] ?? 'Unassigned'); ?>" required>
                            <div id="searchResults" class="search-results"></div>
                            <input type="hidden" id="assignedTo" name="assignedTo" value="<?= htmlspecialchars($task['assignedTo']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="tag">Urgency:</label>
                            <?php foreach ($tags as $value): ?>
                                <label class='t-urgency-e'>
                                    <input type='radio' name='tag' value='<?= $value ?>' <?= $task['tag'] === $value ? 'checked' : '' ?> /> <?= $value ?>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-group">
                            <label for="progress">Progress:</label>
                            <select class="form-control" name="progress" required>
                                <option value="<?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                    <?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                                <?php foreach ($progressStatuses as $status): ?>
                                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="due_date">Due Date:</label>
                            <input type="date" class="form-control" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="due_time">Due Time:</label>
                            <input type="time" class="form-control" name="due_time" value="<?php echo htmlspecialchars($task['due_time']); ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Task</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='space_home.php?grade=<?= $grade?>'">Cancel</button>
                </form>
            </div>
        </div>
    

    

        <?php include '../display_mod.php'; ?>

        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

        
        <script src='../../src/js/datetime.js'></script>
        <script src="../../src/js/toggleSidebar.js"></script>
        <script src="../../src/js/verify.js"></script>
        <script src="../../src/js/new_sy.js"></script>
        <script src='../../src/js/notification.js'></script>
        <script src='../../src/js/message.js'></script>

        <script>
            function openDiscardChangesModal() {
                $('#discardChangesModal').modal('show');
            }

            $('#confirmDiscardButton').on('click', function() {
                window.location.href = 'manage_events.php'; 
            });

            $(document).ready(function() {
                $('#assignedToSearch').on('keyup', function() {
                    let query = $(this).val();
                    let grade = '<?= $grade ?>';  // Ensure this outputs the correct grade value

                    if (query.length > 1) {
                        $.ajax({
                            url: '../../src/processes/a/search_users_by_grade.php',
                            method: 'POST',
                            data: { query: query, grade: grade },
                            success: function(data) {
                                $('#searchResults').html(data);
                            },
                            error: function(xhr, status, error) {
                                console.log("Error:", error);  
                                $('#searchResults').html('<div class="search-result-item">Error occurred. Please try again.</div>');
                            }
                        });
                    } else if (query.length === 0) {
                        $('#searchResults').html('');  
                    } 
                });

                // When a search result is clicked, populate the hidden input with user ID
                $(document).on('click', '.search-result-item', function() {
                    let userId = $(this).data('userid');
                    let userInfo = $(this).text();
                    
                    $('#assignedToSearch').val(userInfo);  // Set the visible field
                    $('#assignedTo').val(userId);  // Set the hidden field with user ID
                    $('#searchResults').html('');  // Clear the search results
                });
            });
        </script>
    </div>
   
</body>
</html>
