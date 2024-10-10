<?php
// get_due_tasks.php
require_once __DIR__ . '/../../config/config.php'; // Includes general configuration
require_once __DIR__ . '/../../config/db_config.php'; // Includes database configuration
require_once __DIR__ . '/../../config/session_config.php'; // Includes session configuration

function getDueTasks($assignedTo)
{
    global $pdo; // Use the PDO instance from db_config.php
    $tasks = [];

    // Get current date and the date 3 days from now
    $currentDate = date('Y-m-d');
    $datePlus3Days = date('Y-m-d', strtotime('+3 days'));

    try {
        // Prepare and execute the query to fetch tasks assigned to the current user
        $query = "
            SELECT 
                tasks.*, 
                assignedByUser.username AS assigned_by_username, 
                assignedToUser.username AS assigned_to_username 
            FROM tasks 
            LEFT JOIN users AS assignedByUser ON tasks.assignedBy = assignedByUser.id 
            LEFT JOIN users AS assignedToUser ON tasks.assignedTo = assignedToUser.id 
            WHERE tasks.assignedTo = :assignedTo 
            AND tasks.due_date BETWEEN :currentDate AND :datePlus3Days
        ";
    
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':assignedTo', $assignedTo, PDO::PARAM_INT);
        $stmt->bindParam(':currentDate', $currentDate);
        $stmt->bindParam(':datePlus3Days', $datePlus3Days);
        $stmt->execute();

        // Fetch all matching tasks
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Log the error and return an empty array
        log_error('Failed to fetch tasks: ' . $e->getMessage(), 'task_errors.txt');
        return []; // Return an empty array on error
    }

    return $tasks; // Return the fetched tasks
}

// Assuming the user ID is stored in the session
if (isset($_SESSION['user_id'])) {
    $assignedTo = $_SESSION['user_id']; 
    $tasks = getDueTasks($assignedTo); 
} else {
    $tasks = []; 
}
// var_dump($tasks);

?>
<!-- <div class="task-grid">
    <?php foreach ($tasks as $task): ?>
        <div class="task-card">
            <div class="r1">
                <?php $color = isset($task['tag']) ? getUrgencyColor($task['tag']) : 'gray'; ?>
                <?php $task_type = isset($task['taskType']) ? getTaskType($task['taskType']) : ''; ?>
                <div class="urgency-circle" style="background-color: <?= htmlspecialchars($color) ?>" title="<?= htmlspecialchars($task['tag'] ?? '') ?>"></div>
                <div class="task-title"><?= htmlspecialchars($task['title'] ?? 'Untitled Task') ?></div>
                <div class="task-lock"><i class="<?= htmlspecialchars($task_type) ?>" title="<?= htmlspecialchars($task['taskType']) ?>"></i></div>
                <div class="edit-button">
                    <?php if ($task['taskType'] == 'assigned'): ?>
                        <a href="update_tasks.php?grade=<?= $task['grade'] ?>&id=<?= $task['id'] ?>"><i class="bi bi-pencil-square"></i></a>
                    <?php else: ?>
                        <a href="update_my_task.php?id=<?= $task['id'] ?>" title="Edit task"><i class="bi bi-pencil-square"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="r2">
                <div class="task-label">Due Date</div>
                    <div class="task-due-date" title="Due date"><?= $task['due_date'] ? date('F j', strtotime($task['due_date'])) : 'None' ?></div>
                    <div class="task-due-time" title="Due time">
                        <?= !empty($task['due_time']) ? date('h:i A', strtotime($task['due_time'])) : 'No Due Time' ?>
                    </div>
                </div>

            <div class="r3">
                <div class="task-label">Progress</div>
                <div class="task-data progress-input">
                    <form action="update_task_progress.php" class="task-upd-f" method="post">
                        <input type="hidden" name="grade" value="<?= $task['grade'] ?>">   
                        <select class="task-data-select" data-task-id="<?= $task['id'] ?>" >
                            <option value="<?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                <?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <option value="pending" <?= $task['progress'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="in_progress" <?= $task['progress'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="completed" <?= $task['progress'] == 'completed' ? 'selected' : '' ?>>Completed</option>      
                        </select>
                    </form>
                </div>
            </div>

            <div class="r3">
                <div class="task-label">Assigned To</div>
                <div class="task-data"><?= htmlspecialchars($task['assigned_to_username'] ?? ''); ?></div>
            </div>

            <div class="r3">
                <div class="task-label">Assigned by</div>
                <div class="task-data"><?= htmlspecialchars($task['assigned_by_username']) ?></div>
            </div>
 -->
            <!-- <div class="r3">
                <div class="task-label">Space</div>
                <?php 
                    $spaceToDisplay = htmlspecialchars($task['grade']) === 'SNED' ? 'SNED' : 'Grade ' . htmlspecialchars($task['grade']);
                ?>
                <div class="task-data"><?= $spaceToDisplay ?></div>
            </div> -->

            <!-- <div class="r4">
                <div class="task-label-r4">Description</div>
                <div class="task-data"><?= htmlspecialchars($task['description'] ?? 'None');?></div>
            </div>

            <div class="p-task-action-con">
                <div class="task-action-reminder">
                    <form action="../../src/processes/remind_me.php" method="POST">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($task['id'] ?? ''); ?>">
                        <input type="hidden" name="rtype" value="<?= htmlspecialchars($task['taskType'] ?? ''); ?>">
                        <input type="hidden" name="utyp" value="am">
                        <button type="button" title="Set reminder for this task" class="btn" style="display: inline;" 
                            data-bs-toggle="modal" data-bs-target="#setReminderModal"
                            data-task-title="<?= htmlspecialchars($task['title'] ?? ''); ?>"
                            data-task-due="<?= htmlspecialchars($task['due_date'] ?? ''); ?>"
                            data-task-id="<?= htmlspecialchars($task['id'] ?? ''); ?>"
                            data-task-rtype="<?= htmlspecialchars($task['taskType'] ?? ''); ?>"
                            data-task-utyp="am"
                            data-task-rtypetask="task">
                                <i class="bi bi-bell"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div> -->

<!-- <div class="task-list-container"> -->
    <?php if (empty($tasks)): ?>
        <div style="color:darkslategrey;"><?php echo 'You have no due tasks yet :)'?></div>
    <?php else: ?>
        <div class="task-grid">
            <?php foreach ($tasks as $task): ?>
                <?php if($task['taskType'] === 'private') {?>
                    <div class="task-card">
                        <div class="r1">
                            <?php $color = isset($task['tag']) ? getUrgencyColor($task['tag']) : 'gray'; ?>
                            <?php $task_type = isset($task['taskType']) ? getTaskType($task['taskType']) : '';?>
                            <div class="urgency-circle" style="background-color: <?= htmlspecialchars($color) ?>" title="<?= htmlspecialchars($task['tag'] ?? '') ?>"></div>
                            <div class="task-title"><?= htmlspecialchars($task['title'] ?? 'Untitled Task') ?></div>
                            <div class="task-lock"><i class="<?php echo htmlspecialchars($task_type)?>" title="<?php echo htmlspecialchars($task['taskType'])?>"></i></div>
                            <div class="edit-button">
                                <a href="update_my_task.php?id=<?= $task['id'] ?>" title="Edit task"><i class="bi bi-pencil-square"></i></a>
                            </div>
                        </div>

                        <div class="r2">
                            <div class="task-label">Due Date</div>
                            <div class="task-due-date" title="Due date"><?= $task['due_date'] ? date('F j', strtotime($task['due_date'])) : 'None' ?></div>
                            <div class="task-due-time" title="Due time"><?= $task['due_time'] ? date('h:i A', strtotime($task['due_time'])) : 'None' ?></div>
                        </div>

                        <div class="r3">
                            <div class="task-label">Progress</div>
                            <div class="task-data progress-input">
                                <form action="update_task_progress.php" class="task-upd-f" method="post"> 
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">   
                                    <select class="task-data-select" data-task-id="<?= $task['id'] ?>" >
                                            <option value="<?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                                <?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                            <option value="pending" <?= $task['progress'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="in_progress" <?= $task['progress'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="completed" <?= $task['progress'] == 'completed' ? 'selected' : '' ?>>Completed</option>      
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="r4">
                            <div class="task-label-r4">Description</div>
                            <div class="task-data"><?= htmlspecialchars($task['description'] ?? 'None') ?></div>
                        </div>

                        <div class="p-task-action-con">
                            <div class="task-action-delete">
                                <form action="../../src/processes/a/delete_my_tasks.php" method="POST" id="delete-button">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($task['id'] ?? '');?>">
                                    <button type="button" title="Delete task" class="btn delete-button" data-form-id="delete-button" style="display: inline;" onclick="confirmDeleteModal()">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="task-action-reminder">
                                <form action="../../src/processes/remind_me.php" method="POST">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($task['id'] ?? '');?>">
                                    <input type="hidden" name="rtype" value="<?= htmlspecialchars($task['taskType'] ?? '');?>">
                                    <input type="hidden" name="utyp" value="ur">
                                    <button type="button" title="Set reminder for this task" class="btn" style="display: inline;" 
                                        data-bs-toggle="modal" data-bs-target="#setReminderModal"
                                        data-task-title="<?= htmlspecialchars($task['title'] ?? ''); ?>"
                                        data-task-due="<?= htmlspecialchars($task['due_date'] ?? ''); ?>"
                                        data-task-id="<?= htmlspecialchars($task['id'] ?? ''); ?>"
                                        data-task-rtype="<?= htmlspecialchars($task['taskType'] ?? ''); ?>"
                                        data-task-utyp="ur"
                                        data-task-rtypetask="task">
                                            <i class="bi bi-bell"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php } else if ($task['taskType'] === 'assigned') {?>
                    <div class="task-card">

                        <div class="r1">
                            <?php $color = isset($task['tag']) ? getUrgencyColor($task['tag']) : 'gray';?>
                            <?php $task_type = isset($task['taskType']) ? getTaskType($task['taskType']) : '';?>
                            <div class="urgency-circle" style="background-color: <?php echo htmlspecialchars($color) ?? 'None'; ?>" title="<?php echo htmlspecialchars($task['tag'] ?? '')?>"></div>
                            <div class="task-title"><?php echo htmlspecialchars($task['title'] ?? 'Untitled Task'); ?></div>
                            <div class="task-lock"><i class="<?php echo htmlspecialchars($task_type)?>" title="<?php echo htmlspecialchars($task['taskType'])?>"></i></div>
                            <!-- <div class="edit-button">
                                <a href="update_tasks.php?grade=<?=$grade?>&id=<?=$task['id'] ?>"><i class="bi bi-pencil-square"></i></a>
                            </div> -->
                        </div>

                        <div class="r2">
                            <div class="task-label">Due Date</div>
                            <div class="task-due-date" title="Due date">
                                <?php echo $task['due_date'] ? date('F j', strtotime($task['due_date'])) : 'None'; ?>
                            </div>
                            <div class="task-due-time" title="Due time">
                                <?php echo $task['due_time'] ? date('h:i A', strtotime($task['due_time'])) : 'None'; ?>
                            </div>
                        </div>

                        <div class="r3">
                            <div class="task-label">Progress</div>
                            <div class="task-data progress-input">
                                <form action="update_task_progress.php" class="task-upd-f" method="post"> 
                                    <input type="hidden" name="grade" value="<?= $task['grade']?>">   
                                    <select class="task-data-select" data-task-id="<?= $task['id'] ?>" >
                                            <option value="<?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>" selected>
                                                <?= htmlspecialchars($task['progress'], ENT_QUOTES, 'UTF-8') ?>
                                            </option>
                                            <option value="pending" <?= $task['progress'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="in_progress" <?= $task['progress'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                            <option value="completed" <?= $task['progress'] == 'completed' ? 'selected' : '' ?>>Completed</option>      
                                    </select>
                                </form>
                            </div>
                        </div>

                        <div class="r3">
                            <div class="task-label">Assigned To</div>
                            <div class="task-data"><?php echo htmlspecialchars($task['assigned_to_username'] ?? ''); ?></div>
                        </div>

                        <div class="r3">
                            <div class="task-label">Assigned by</div>
                            <div class="task-data"><?php echo htmlspecialchars($task['assigned_by_username'])?></div>
                        </div>

                        <div class="r3">
                            <div class="task-label">Space</div>
                            <?php 
                                if (htmlspecialchars($task['grade']) === 'SNED') {
                                    $spaceToDisplay = 'SNED';
                                }else {
                                    $spaceToDisplay = 'Grade ' . htmlspecialchars($task['grade']);
                                }
                            ?>
                            <div class="task-data"><?php echo $spaceToDisplay?></div>
                        </div>

                        <div class="r4">
                            <div class="task-label-r4">Description</div>
                            <div class="task-data"><?php echo htmlspecialchars($task['description'] ?? 'None');?></div>
                        </div>

                        <div class="p-task-action-con">
                            <!-- <div class="task-action-deactivate">
                                <form action="../../src/processes/delete_task.php" method="POST">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($task['id'] ?? '');?>">
                                    <input type="hidden" name="grade" value="<?= htmlspecialchars($task['grade']) ?? ''?>">
                                    <button type="submit" title="Discard Task" class="btn btn-normal" style="display: inline;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div> -->
                            <div class="task-action-reminder">
                                <form action="../../src/processes/remind_me.php" method="POST">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($task['id'] ?? '');?>">
                                    <input type="hidden" name="rtype" value="<?= htmlspecialchars($task['taskType'] ?? '');?>">
                                    <input type="hidden" name="utyp" value="ur">
                                    <button type="button" title="Set reminder for this task" class="btn" style="display: inline;" 
                                        data-bs-toggle="modal" data-bs-target="#setReminderModal"
                                        data-task-title="<?= htmlspecialchars($task['title'] ?? ''); ?>"
                                        data-task-due="<?= htmlspecialchars($task['due_date'] ?? ''); ?>"
                                        data-task-id="<?= htmlspecialchars($task['id'] ?? ''); ?>"
                                        data-task-rtype="<?= htmlspecialchars($task['taskType'] ?? ''); ?>"
                                        data-task-utyp="ur"
                                        data-task-rtypetask="task">
                                            <i class="bi bi-bell"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php    }
            endforeach; ?>

            <!-- <div class="btn-add-cont">
                <div class="btn-add-bottom">
                    <a href="add_task.php?_personal" title="Add new personal task"><i class="bi bi-plus-circle"></i></a>
                </div>
            </div> -->

        </div>
    <?php endif; ?>
<!-- </div> -->