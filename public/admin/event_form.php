<?php
require_once __DIR__ . '/../../src/config/session_config.php';
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/db_config.php';
require_once __DIR__ . '/../../src/config/config.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$event = [
    'title' => '',
    'description' => '',
    'start' => '',
    'end' => ''
];

if ($id) {
    // Fetch event details for editing
    $stmt = $pdo->prepare("SELECT id, title, description, event_date as start, end_date as end FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Event</title>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css' rel='stylesheet' />
</head>
<body>

    <h2><?php echo $id ? 'Edit' : 'Add'; ?> Event</h2>

    <form action="<?php echo $id ? '../../src/processes/a/update_event.php' : '../../src/processes/a/add_event.php'; ?>" method="POST">
        <?php if ($id): ?>
            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
        <?php endif; ?>
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="start">Start Date:</label>
            <input type="date" class="form-control" id="start" name="start" value="<?php echo htmlspecialchars($event['start']); ?>" required>
        </div>
        <div class="form-group">
            <label for="end">End Date:</label>
            <input type="date" class="form-control" id="end" name="end" value="<?php echo htmlspecialchars($event['end']); ?>">
        </div>
        <button type="submit" class="btn btn-primary"><?php echo $id ? 'Update' : 'Add'; ?> Event</button>
    </form>

    <script src='https://code.jquery.com/jquery-3.5.1.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
</body>
</html>
