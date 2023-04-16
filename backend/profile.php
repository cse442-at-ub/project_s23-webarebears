
<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>My Website</title>
    <link rel="stylesheet" href="styles/home_style.css"/>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body id="homepage">
	<header>
		<nav class="nav-left">
			<img href="" id="profile-pic" src="images/profile-temp.png">
			<a href="home.html" id="home" >Home</a>
			<a id="tasksAndBalances" href="tasksAndBalances.html">Tasks and Balances</a>
			<a id="messages" href="messages.html">Messages</a>			
		</nav>
		<nav class="nav-right">
			<input id="search-bar" type="search" placeholder="Search">
			<button type="button" class="icon-button">
				<span class="material-icons">notifications</span>
				<span class="icon-button__badge">2</span>
			</button>
		</nav>
		
	</header>
</body>
</html>

