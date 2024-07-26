<?php
require_once __DIR__ . '/../../src/config/access_control.php'; // Include access control script
require_once __DIR__ . '/../../src/config/session_config.php';

//Check if the user is an admin
check_access('ADMIN');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-s
    cale=1.0">
    <title>Add New User</title>
</head>
<body>
    <h1>Add New User</h1>
    <form action="../../src/processes/add_user_process.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" required><br><br>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="gradeLevel">Grade Level:</label>
        <input type="text" id="gradeLevel" name="gradeLevel" required><br><br>

        <label for="section">Section:</label>
        <input type="text" id="section" name="section" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
        </select><br><br>

        <label for="accType">Account Type:</label>
        <select id="accType" name="accType" required>
            <option value="USER">User</option>
            <option value="ADMIN">Admin</option>
        </select><br><br>

        <input type="submit" value="Add User">
    </form>
</body>
</html>
