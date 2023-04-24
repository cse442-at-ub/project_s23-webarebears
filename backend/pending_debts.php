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
        SELECT debt_id, description, amount, due_date
        FROM Users_Debts
        WHERE assigned_to = '$user_id' AND status = 'pending'
        ORDER BY due_date ASC
    ";

    $result = mysqli_query($db_connection, $query);

    $pending_debts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $pending_debts[] = $row;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pending Debts</title>
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
            <a id="tasksAndBalances" href="balances.php">Balances</a>
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
            <h2>Pending Debts</h2>
            <ul class="pending-debts">
                <?php foreach ($pending_debts as $debt): ?>
                    <li>
                        <h4><?php echo htmlspecialchars($debt['description']); ?></h4>
                        <span> - Amount: <?php echo htmlspecialchars($debt['amount']); ?></span>
                        <span> - Due on: <?php echo htmlspecialchars($debt['due_date']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
</body>
</html>
