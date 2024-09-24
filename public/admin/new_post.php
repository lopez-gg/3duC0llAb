<?php
// new_post.php
require_once __DIR__ . '/../../src/config/access_control.php';
require_once __DIR__ . '/../../src/config/session_config.php';

// Admin access check
check_access('ADMIN');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /public/login.php');
    exit;
} else {
    if(isset($_GET['grade'])){
        $grade = isset($_GET['grade']) ? trim($_GET['grade']) : '';
        $_SESSION['grade'] = $grade;

        if (is_numeric($grade) && $grade >= 1 && $grade <= 6) {
            $gradetodisplay = 'Grade ' . intval($grade);
        } elseif (strtolower($grade) === 'sned') {
            $gradetodisplay = strtoupper($grade);
        } else {
            $gradetodisplay = 'Unknown Grade'; 
        }
        
    } elseif(isset($_GET['forum'])){
        $forum = isset($_GET['forum']) ? trim($_GET['forum']) : '';
        $grade = $forum;
        $gradetodisplay = 'PSCS General';
    }
}


// Set default values for the variables
$currentDateTime = date('l, d/m/Y h:i:s A'); 


$successTitle = isset($_SESSION['success_title']) ? $_SESSION['success_title'] : null;
$successMessage = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null;
$verificationMessage = isset($_SESSION['verification_message']) ? $_SESSION['verification_message'] : null;
$errorMessage = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null;
include '../display_mod.php';
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Forum Post</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link href="../../src/css/gen.css" rel="stylesheet">
    <link href="../../src/css/a/dashb.css" rel="stylesheet">
</head>
<body>
    <?php include '../nav-sidebar-temp.php'?>

        <div class="content" id="content">
            <h2 class="mb-4"> <?php echo htmlspecialchars($gradetodisplay); ?> Forum > Create new post</h2>


    
            <section class="main-sec">                           
                <?php if ($errorMessage): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($errorMessage); ?>
                    </div>
                <?php endif; ?>

                <form action="../../src/processes/a/add_forum_post.php" method="POST">
                    <input type="hidden" name="grade" value="<?= htmlspecialchars($grade); ?>">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Post</button>
                </form>
            </section>
        </div>
    </div>
    

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
         $(window).on('load', function() {
                <?php if ($successMessage): ?>
                    $('#successModal').modal('show');
                    setTimeout(function() {
                        $('#successModal').modal('hide');
                    }, 4500);
                <?php endif; ?>
            });
    </script>
</body>
</html>
