<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

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
    <link rel="stylesheet" href="styles/home_style.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body id="homepage">
    <header>
        <nav class="nav-left">
        <a href="profile.php">
				<img id="profile-pic" src="images/profile-temp.png" alt="Profile Icon">
			</a>
            <a href="home.php" id="home">Home</a>
            <a id="tasksAndBalances" href="tasksAndBalances.php">Tasks and Balances</a>
            <a id="messages" href="messages.php">Messages</a>         
        </nav>
        <nav class="nav-right">
            <input id="search-bar" type="search" placeholder="Search">
            <button type="button" class="icon-button">
                <span class="material-icons">notifications</span>
                <span class="icon-button__badge">2</span>
            </button>
        </nav>
    </header>
    <main>
    <div class="box-container">
            <h2>Pending Tasks</h2>
            <ul class="pending-tasks">
                <?php foreach ($pending_tasks as $task): ?>
                    <li>
                        <input type="checkbox" data-task-id="<?php echo $task['task_id']; ?>">
                        <h4><?php echo htmlspecialchars($task['description']); ?></h4>
                        <span> - Due: <?php echo htmlspecialchars($task['due_date']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
            <button id="complete-tasks-btn" onclick="completeTasks()">Complete</button>
        </div>
    </main>

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
</body>
</html>



