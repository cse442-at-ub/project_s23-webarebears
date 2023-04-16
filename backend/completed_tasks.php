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
            <h2>Completed Tasks</h2>
            <ul class="completed-tasks">
                <?php foreach ($completed_tasks as $task): ?>
                    <li>
                        <h4><?php echo htmlspecialchars($task['description']); ?></h4>
                        <span> - Completed on: <?php echo htmlspecialchars($task['due_date']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
</body>
</html>
