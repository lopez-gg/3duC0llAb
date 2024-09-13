<?php 
require_once __DIR__ . '/../../config/db_config.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/session_config.php';

$id = $_POST['id'] ?? null;

if (empty($id)) {
    $_SESSION['success_message'] = 'Invalid task ID.';
    exit;
}

echo $id;

?>