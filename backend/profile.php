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
    <title>Profile Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito&display=swap" />
    <link rel="stylesheet" href="styles/profiletab.css"/>
  </head>
  <body>
    <header>
      <h1>Profile Page</h1>
    </header>
    <main>
      <div class="profile-tab">
        <div class="profile-header">
            <img id="logo" class="profile-picture" src="images/profile-temp.png" alt="Logo">
          <h2 class="profile-name"><?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        </div>

        <div class="profile-stats">
            <div class="stat-container left-stat">
              <span class="stat-number">

			  	<?php
				require('server.php');
				session_start();

				if (!isset($_SESSION['username'])) {
					header("Location: login.php");
					exit();
				}

				$username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
				$query = "SELECT friend_username FROM `Friends` WHERE username='$username'";
				$result = mysqli_query($db_connection, $query);

				$num_friends = mysqli_num_rows($result);

				echo . $num_friends .;
				?>

			  </span>

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
            <button class="profile-button edit-button" style="background-color: #343645;">Edit Profile</button>
            <button class="profile-button settings-button" style="background-color: #343645;">Settings</button>
        </div>

        <div class="list-container">
            <ul>
              <li><i class="fas fa-check-circle"></i> <a href="completed_debts.php">Completed Debts</a></li>
              <li><i class="fas fa-tasks"></i> <a href="completed_tasks.php">Completed Tasks</a></li>
              <li><i class="fas fa-clock"></i> <a href="pending_debts.php">Pending Tasks</a></li>
              <li><i class="fas fa-money-bill-wave"></i> <a href="pending_tasks.php">Pending Debts</a></li>
              <li><i class="fas fa-info-circle"></i> <a href="about_us.php">About Us</a></li>
              <li><i class="fas fa-file-alt"></i> <a href="terms_and_conditions.php">Terms and Conditions</a></li>
            </ul>
        </div>

        <button method="post" action="logout.php" class="logout-button">Log Out</button>
      </div>

      <div class="content">
        <!-- Add your content on the right side of the screen here -->
      </div>
    </main>
  </body>
</html>

