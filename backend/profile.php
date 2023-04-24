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
    <title>Profile Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito&display=swap" />
    <link rel="stylesheet" href="styles/profiletab.css"/>
  </head>
  <body>

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
      <div class="profile-tab">

        <div class="profile-header">
            <img id="logo" class="profile-picture" src="images/profile-temp.png" alt="Logo">
          <h2 class="profile-name"><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        </div>

        <div class="profile-stats">
          <div class="stat-container left-stat">
            <span class="stat-number">0</span>

            <br>

            <span class="stat-label">Friends</span>
          </div>
          <div class="middle-line"></div>
          <div class="stat-container right-stat">
            <span class="stat-number">$0</span>
            <br>
            <span class="stat-label">Balance</span>
          </div>
        </div>
          
        <div class="button-container">
            <button onclick="location.href='edit_profile.php'" class="profile-button edit-button" style="background-color: #343645;">Edit Profile</button>
            <button onclick="location.href='settings.php'" class="profile-button settings-button" style="background-color: #343645;">Settings</button>
        </div>

        <div class="list-container">
            <ul>
              <li><i class="fas fa-check-circle"></i> <a href="completed_debts.php">Completed Debts</a></li>
              <li><i class="fas fa-tasks"></i> <a href="completed_tasks.php">Completed Tasks</a></li>
              <li><i class="fas fa-clock"></i> <a href="pending_tasks.php">Pending Tasks</a></li>
              <li><i class="fas fa-money-bill-wave"></i> <a href="pending_debts.php">Pending Debts</a></li>
              <li><i class="fas fa-info-circle"></i> <a href="about_us.php">About Us</a></li>
              <li><i class="fas fa-file-alt"></i> <a href="terms_and_conditions.php">Terms and Conditions</a></li>
            </ul>
        </div>
		<form method="post" action="" id='log-out-button'>
                    <input type="submit" name="logout" value="Logout">
        </form>

      </div>

      <div class="content">
        <!-- Add your content on the right side of the screen here -->
      </div>
    </main>
  </body>
</html>