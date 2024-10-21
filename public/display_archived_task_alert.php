<?php 
require_once __DIR__ . '/../src/config/db_config.php';
require_once __DIR__ . '/../src/processes/check_del_assgnd_tasks.php'; // Include here to fetch tasks

// Check if there are pending tasks
if (!empty($tasks)) {
    // Prepare task details for the modal
    $taskDetails = '';
    foreach ($tasks as $task) {
        // Construct task details HTML
        if (is_numeric($task['grade'])) {
            $gradetodisplay = 'Grade ' . $task['grade'];
        } elseif (isset($task['grade']) && is_string($task['grade']) && strtolower($task['grade']) === 'sned') {
            $gradetodisplay = $task['grade'];
        } else {
            $gradetodisplay = 'None'; 
        }

        $taskDetails .= "
        <div class='task-card'>
            <div class='r1'>
                <div class='urgency-circle' style='background-color: gray;' title=''></div>
                <div class='task-title'>" . htmlspecialchars($task['title'] ?? 'Untitled Task') . "</div>
                <div class='task-lock'><i class='' title=''></i></div>
            </div>
            <div class='r2'>
                <div class='task-label'>Due Date</div>
                <div class='task-due-date'>" . (!empty($task['due_date']) ? $task['due_date'] : 'None')   . "</div>
                <div class='task-due-time'>" . (!empty($task['due_time']) ? $task['due_time'] : 'None')   . "</div>
            </div>
            <div class='r3'>
                <div class='task-label'>Progress</div>
                <div class='task-data'>" . htmlspecialchars($task['progress']) . "</div>
            </div>
            <div class='r3'>
                <div class='task-label'>Assigned To</div>
                <div class='task-data'>" . htmlspecialchars($task['assignedToFirstName'] . ' ' . $task['assignedToLastName'] ?? '') . "</div>
            </div>
            <div class='r3'>
                <div class='task-label'>Assigned By</div>
                <div class='task-data'>" . htmlspecialchars($task['assignedByFirstName'] . ' ' . $task['assignedByLastName'] ?? '') . "</div>
            </div>
            <div class='r3'>
                <div class='task-label'>Space</div>
                <div class='task-data'>" . htmlspecialchars($gradetodisplay ?? '') . "</div>
            </div>
            <div class='r4'>
                <div class='task-label-r4'>Description</div>
                <div class='task-data'>" . (!empty($task['description']) ? $task['description'] : 'None')  . "</div>
            </div>
        </div>";
    }
}
?>

<!-- Modal -->
<div class="modal fade" id="taskArchivedModal" tabindex="-1" aria-labelledby="taskArchivedModalLabel" aria-hidden="true" data-bs-backdrop="static" data-keyboard="false" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskArchivedModalLabel">Archived Task Alert</h5>
            </div>
            <div class="modal-body archived-alert">
                <p><?php echo !empty($taskDetails) ? 'Admin has deleted the following task: ' : 'No tasks were deleted.'; ?></p>
                <hr>
                <p id="taskDetails"><?php echo !empty($taskDetails) ? $taskDetails : 'No pending tasks.'; ?></p>
            </div>
            <div class="modal-footer">
                <form action="../../src/processes/update_task_alert_status.php" method="POST">
                    <input type="hidden" name="rchived_task_id" value="<?= $task['id'] ?>"> 
                    <button type="submit" class="btn btn-primary" id="archiveConfirmBtn">Okay, got it</button>
                </form>
            </div>

        </div>
    </div>
</div>

<?php
// Use a script tag to show the modal on page load if tasks are found
if (!empty($tasks)) {
    echo "<script>$(document).ready(function() { $('#taskArchivedModal').modal('show'); });</script>";
}
?>
