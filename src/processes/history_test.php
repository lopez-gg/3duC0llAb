<?
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/session_config.php';

    function add_activity_history ($userID, $subjectID, $activity_message){
        global $pdo;
        try{
             // Insert a notification for the request_owner
            $history_sql = "INSERT INTO activities_history (user_id, subject_id, message, created_at) 
            VALUES (:user_id, :subject_id, :message, NOW())";
       
            $history_stmt = $pdo->prepare($history_sql);
            $history_stmt->bindParam(':user_id', $userID, PDO::PARAM_INT);
            $history_stmt->bindParam(':subject_id', $subjectID, PDO::PARAM_INT);
            $history_stmt->bindParam(':message', $activity_message, PDO::PARAM_STR);

            // Execute the notification insert
            $history_stmt->execute();

        }catch (PDOException $e) {
            log_error('Query failed: ' . $e->getMessage(), 'db_errors.txt');
            exit;
        }
    }


    $events_sql = 'SELECT id FROM events WHERE added_at = NOW(YYYY-MMMM-DD) AND year_range =' . $sy;
        $events_stmt = $pdo->prepare($events_sql);
        $events_stmt->execute();
        $events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);

        

        echo $subject_id;

?>