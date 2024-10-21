<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';
require_once __DIR__ . '/../../src/processes/check_upcoming_events.php'; 

// Check if the user is admin
check_access('USER');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
} else {
    $userID = $_SESSION['user_id'];
}


$my_space = 'my_space';

// Handle Pagination Variables
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1; 
$itemsPerPage = 10; 
$index = ($currentPage - 1) * $itemsPerPage + 1;
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
$progress = isset($_GET['progress']) ? $_GET['progress'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch tasks from internal function
require_once __DIR__ . '/../../src/processes/a/fetch_my_tasks.php';
$tasksData = fetch_my_tasks($userID, $order, $progress, $search, $currentPage, $itemsPerPage);


if (isset($tasksData['error'])) {
    echo "<p>Error: " . htmlspecialchars($tasksData['error']) . "</p>";
    exit;
}

$tasks = $tasksData['tasks'] ?? [];
$totalPages = $tasksData['totalPages'] ?? 1;


$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = $_SESSION['success_message'] ?? null;
include '../display_mod.php';
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="en">
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Space</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
        <link href="../../src/css/gen.css" rel="stylesheet">
        <link rel="stylesheet" href="../../src/css/tasks.css">
        <link href="../../src/css/action_nav.css" rel="stylesheet">
        <link rel="stylesheet" href="../../src/css/message.css">
    </head>
    <body>
        <?php include '../nav-sidebar-temp.php'?>
            <div class="content" id="content">
                <section class='main-sec' id='sec-one'>
                    <h2>My Personal Tasks</h2>

                    

                </section>

                <hr>
                <section class="actions-section">
                    <div class="right-section-actions">
                        <div class="rs-a">
                            <button class="btn-sort dropdown-toggle" id="sortIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Sort order">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="sortIcon">
                                <a class="dropdown-item sort-option" data-order="asc">Ascending</a>
                                <a class="dropdown-item sort-option" data-order="desc">Descending</a>
                            </div>
                        </div>
                        <div class="rs-a">
                            <button class="btn-filter dropdown-toggle" id="filterIcon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Filter tasks">
                                <i class="bi bi-filter"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="filterIcon">
                                <a class="dropdown-item filter-option" data-progress="">All</a>
                                <a class="dropdown-item filter-option" data-progress="pending">Pending</a>
                                <a class="dropdown-item filter-option" data-progress="in_progress">In Progress</a>
                                <a class="dropdown-item filter-option" data-progress="completed">Completed</a>
                            </div>
                        </div>
                    </div>
                    <div class="left-section-actions">
                        <div class="ls-a">
                        <div class="search-bar">
                            <input type="text" class="searchBox" id="taskSearch" placeholder="Search tasks..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button id="searchButton"><i class="bi bi-search"></i></button>
                        </div>
                        </div>
                        <div class="ls-a">
                        <div class="btn-add">
                            <a href="add_task.php?_personal" id="taskEdit" title="Add new personal task"><i class="bi bi-plus-circle"></i></a>
                        </div>
                        </div>
                    </div>
                </section>
                <hr>
            <!-- Legend Button with Unique ID and Menu -->
            <div class="legend-con" style="display: flex; position: relative; flex-direction: row-reverse;">
                <button class="btn legendBtn" id="legend" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Filter tasks">
                    <i class="bi bi-patch-question"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="legend" style="z-index:9999;">
                    <ul class="legend-list-group" style="max-width:300px; list-style:none; padding:10px; margin:0;">
                        <li class="legend-list-group-item">
                            <span class="badge" style="background-color: white; border: 1px solid gray;">&nbsp;&nbsp;</span> - Normal
                        </li>
                        <li class="legend-list-group-item">
                            <span class="badge" style="background-color: yellow;">&nbsp;&nbsp;</span> - Urgent
                        </li>
                        <li class="legend-list-group-item">
                            <span class="badge" style="background-color: orange;">&nbsp;&nbsp;</span> - Important
                        </li>
                        <li class="legend-list-group-item">
                            <span class="badge" style="background-color: red;">&nbsp;&nbsp;</span> - Urgent and Important
                        </li>  
                    </ul>
                </div>
            </div>


                <div class="task-list-container">
                    <?php if (empty($tasks)): ?>
                        <div>No personal tasks found.</div>
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
                                            <div class="edit-button">
                                                <a href="update_tasks.php?grade=<?=$grade?>&id=<?=$task['id'] ?>"><i class="bi bi-pencil-square"></i></a>
                                            </div>
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
                                            <div class="task-data"><?php echo htmlspecialchars($task['assigned_username'] ?? ''); ?></div>
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

                            <div class="btn-add-cont">
                                <div class="btn-add-bottom">
                                    <a href="add_task.php?_personal" title="Add new personal task"><i class="bi bi-plus-circle"></i></a>
                                </div>
                            </div>

                        </div>
                    <?php endif; ?>
                </div>

                <section class="main-sec" id="page-nav">
                    <nav aria-label="Task pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                <li class="page-item <?= $page == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $page ?>"><?= $page ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </section>

            </div>
        </div>

        <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
        <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        
        <script src='../../src/js/datetime.js'></script>
        <script src='../../src/js/notification.js'></script>
        <script src='../../src/js/toggleSidebar.js'></script>
        <script src='../../src/js/message.js'></script>
        <script src='../../src/js/verify.js'></script>>
        <script src='../../src/js/reminder.js'></script>

        <script>
            $(window).on('load', function() {
                <?php if ($successMessage): ?>
                    $('#successModal').modal('show');
                    setTimeout(function() {
                        $('#successModal').modal('hide');
                    }, 4500);
                <?php endif; ?>
            });


            $(document).on('click', '.delete-button', function() {
                var formId = $(this).data('form-id');
                confirmDeleteModal(formId, 'Confirm Deletion', 'Are you sure you want to delete this task?', 'Delete');
             });

            $(document).ready(function () {
            // Handle sorting
            $('.sort-option').on('click', function () {
                const order = $(this).data('order');
                const progress = "<?php echo isset($_GET['progress']) ? urlencode($_GET['progress']) : ''; ?>"; 
                const params = {
                    order: order,
                    progress: progress,
                    page: "<?php echo $currentPage; ?>"
                };
                const queryString = $.param(params);
                window.location.href = `my_space.php?${queryString}`;
            });

            // Handle filtering
            $('.filter-option').on('click', function () {
                const progress = $(this).data('progress');
                const order = "<?php echo isset($_GET['order']) ? urlencode($_GET['order']) : 'desc'; ?>";
                const params = {
                    order: order,
                    progress: progress,
                    page: "<?php echo $currentPage; ?>"
                };
                const queryString = $.param(params);
                window.location.href = `my_space.php?${queryString}`;
            });
        });
          // Handle searching
        $('#searchButton').on('click', function () {
            const searchQuery = $('#taskSearch').val();
            const order = "<?php echo isset($_GET['order']) ? urlencode($_GET['order']) : 'desc'; ?>";
            const progress = "<?php echo isset($_GET['progress']) ? urlencode($_GET['progress']) : ''; ?>";
            const params = {
                order: order,
                progress: progress,
                search: searchQuery,
                page: "<?php echo $currentPage; ?>"
            };
            const queryString = $.param(params);
            window.location.href = `my_space.php?${queryString}`;
        });
        
        // Handle "Enter" key in search input
        $('#taskSearch').on('keypress', function (e) {
            if (e.which == 13) {
                $('#searchButton').click();
            }
        });

        $(document).ready(function() {
            <?php if ($successMessage): ?>
                $('#successModal').modal('show');
                setTimeout(function() {
                    $('#successModal').modal('hide');
                }, 4500);
            <?php endif; ?>
        });

        $(document).ready(function () {
            $('.task-data-select').on('change', function () {
                const taskId = $(this).data('task-id');
                const newProgress = $(this).val();
                const csrfToken = '<?= $_SESSION['csrf_token'] ?>'; // Assuming CSRF token is set in session

                $.ajax({
                    url: '../../src/processes/update_task_progress.php',
                    type: 'POST',
                    data: {
                        id: taskId,
                        progress: newProgress,
                        csrf_token: csrfToken // Include the CSRF token
                    },
                    success: function (response) {
                        let jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            $('#successModal .modal-body').text(jsonResponse.message);
                    
                            $('#successModal').modal('show');
                            setTimeout(function() {
                                $('#successModal').modal('hide');
                            }, 4500);
                        }
                    },
                    error: function (xhr, status, error) {
                        let jsonResponse = JSON.parse(response);
                        if (jsonResponse.error) {
                            $('#successModal .modal-body').text(jsonResponse.message);
                    
                            $('#successModal').modal('show');
                            setTimeout(function() {
                                $('#successModal').modal('hide');
                            }, 4500);
                        }
                    }
                });
            });
        });

        </script>
    </body>
</html>