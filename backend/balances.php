<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    require('server.php');

    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    } 
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles/balances_style.css"/>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body id="homepage">
	<header>
		<nav class="nav-left">
			<a href="profile.php">
				<img id="profile-pic" src="images/profile-temp.png" alt="Profile Icon">
			</a>
			<a href="home.php" id="home" >Home</a>
			<a id="tasksAndBalances" href="balances.php">Balances</a>
			<a id="messages" href="messages.php">Messages</a>			
		</nav>
		<nav class="nav-right">
            <input id="search-bar" type="search" placeholder="Search">
			<button type="button" class="icon-button">
				<span class="material-icons">notifications</span>
				<span class="icon-button__badge">2</span>
			</button>
            <form method="post" action="" id='log-out-button'>
                <input type="submit" name="logout" value="Logout">
            </form>
		</nav>
	</header>

    <main>
        <div class="debt_owed_to_you">
            <label for="total_owed">Debt Owed To You </label>
            <label id="total_owed">$40</label>
            <p>From Groups</p>
        </div>

        <div class="debt_you_owe">
            <label for="total_dept">Dept you Owe </label>
            <label id="total_dept">$60</label>
            <p>To Groups</p>
        </div>
</body>
</html>