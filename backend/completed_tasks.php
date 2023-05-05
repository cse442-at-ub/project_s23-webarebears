<?php

    require('server.php');

    $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
    $query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];

    $query = "
        SELECT task_id, description, due_date
        FROM Tasks
        WHERE assigned_to = '$user_id' AND status = 'completed'
        ORDER BY due_date ASC
    ";

    $result = mysqli_query($db_connection, $query);

    $completed_tasks = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $completed_tasks[] = $row;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Completed Tasks</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <div class="box-container">
        <h2>Completed Tasks</h2>
        <ul class="completed-tasks">
            <?php foreach ($completed_tasks as $task): ?>
                <div class="task">
                    <h4 style="overflow-wrap: break-word;"><?php echo htmlspecialchars($task['description']); ?></h4>
                    <span> - Completed on: <?php echo htmlspecialchars($task['due_date']); ?></span>
                </div>
            <?php endforeach; ?>
        </ul>
    </div>
</body>


</html>
