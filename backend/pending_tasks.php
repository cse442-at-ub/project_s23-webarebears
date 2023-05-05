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
        WHERE assigned_to = '$user_id' AND status = 'pending'
        ORDER BY due_date ASC
    ";

    $result = mysqli_query($db_connection, $query);

    $pending_tasks = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $pending_tasks[] = $row;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pending Tasks</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
    <div>
        <div class="box-container">
            <h2>Pending Tasks</h2>
            <ul class="pending-tasks">
                <?php foreach ($pending_tasks as $task): ?>
                    <div class="task">
                        <input type="checkbox" data-task-id="<?php echo $task['task_id']; ?>">
                        <h4><?php echo htmlspecialchars($task['description']); ?></h4>
                        <span> - Due: <?php echo htmlspecialchars($task['due_date']); ?></span>
                    </div>
                <?php endforeach; ?>
            </ul>
            <div id="space"></div>
            <button id="complete-tasks-btn" onclick="completeTasks()">Complete</button>
        </div>
    </div>

    <script>
        function completeTasks() {
            const tasksContainer = document.querySelector('.pending-tasks');
            const checkBoxes = tasksContainer.querySelectorAll('input[type=checkbox]:checked');
            const taskIds = [];

            checkBoxes.forEach(checkBox => {
                taskIds.push(checkBox.getAttribute('data-task-id'));
            });

            if (taskIds.length === 0) {
                alert('Please select at least one task to mark as complete.');
                return;
            }

            fetch('completeTasks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ taskIds }),
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        location.reload();
                    } else {
                        console.error('Error completing tasks:', result.error);
                    }
                })
                .catch(error => {
                    console.error('Error completing tasks:', error);
                });
        }
    </script>
</html>



