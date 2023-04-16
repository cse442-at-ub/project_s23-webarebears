
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
		</nav>
	</header>
	<main>
		<div class="box-container">
		<h2><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
			<div class="row">
				<a href="edit_profile.php">Edit Profile</a>
				<a href="settings.php">Settings</a>
				<a href="share_profile.php">Share Profile</a>
			</div>
			<div class="row"><a href="completed_debts.php">Completed Debts</a></div>
			<div class="row"><a href="completed_tasks.php">Completed Tasks</a></div>
			<div class="row"><a href="pending_debts.php">Pending Debts</a></div>
			<div class="row"><a href="pending_tasks.php">Pending Tasks</a></div>
			<div class="row"><a href="help_and_support.php">Help and Support</a></div>
			<div class="row"><a href="about_us.php">About Us</a></div>
			<div class="row"><a href="terms_and_conditions.php">Terms and Conditions</a></div>
			<div class="row">
				<form method="post" action="logout.php">
					<input type="submit" name="logout" value="Logout">
				</form>
			</div>
		</div>
	</main>
</body>
</html>
