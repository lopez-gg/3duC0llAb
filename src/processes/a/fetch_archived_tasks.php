$query = "
    SELECT id, title, description, taskType, tag, grade, progress, status, created_at, due_date
    FROM archived_tasks
    WHERE id = :id";
