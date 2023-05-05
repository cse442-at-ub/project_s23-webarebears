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
  
      //*************************Notification Button Function*****************************//
      $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
      $query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
      $result = mysqli_query($db_connection, $query);
      $user = mysqli_fetch_assoc($result);
      $user_id = $user['user_id'];
  
      $friend_requests_query = "SELECT COUNT(*) as friend_requests_count FROM `Friend_Requests` WHERE receiver_id = '$user_id' AND status = 'pending'";
      $friend_requests_result = mysqli_query($db_connection, $friend_requests_query);
      $friend_requests_row = mysqli_fetch_assoc($friend_requests_result);
      $pending_friend_requests = $friend_requests_row['friend_requests_count'];
  
      $pending_tasks_query = "SELECT COUNT(*) as pending_tasks_count FROM `Tasks` WHERE assigned_to = '$user_id' AND status = 'pending'";
      $pending_tasks_result = mysqli_query($db_connection, $pending_tasks_query);
      $pending_tasks_row = mysqli_fetch_assoc($pending_tasks_result);
      $pending_tasks = $pending_tasks_row['pending_tasks_count'];
  
      $pending_debts_query = "SELECT COUNT(*) as pending_debts_count FROM `Users_Debts` WHERE assigned_to = '$user_id' AND status = 'pending'";
      $pending_debts_result = mysqli_query($db_connection, $pending_debts_query);
      $pending_debts_row = mysqli_fetch_assoc($pending_debts_result);
      $pending_debts = $pending_debts_row['pending_debts_count'];
      //*************************Notification Button Function*****************************//
?>

<!DOCTYPE html>
<html>
  <head>
    <title>Profile Page</title>
    <link rel="stylesheet" href="styles/profiletab.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Spartan' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  <body>
<!--
  <header>
        <nav class="nav-bar">
            <a href="profile.php">
				<img id="profile-pic" src="images/profile-temp.png" alt="Profile Icon">
			</a>
            <a href="home.php" id="home" >Home</a>
            <a id="tasksAndBalances" href="Balances.php">Balances</a>
            <a id="messages" href="messages.php">Messages</a>			

            <input id="search-bar" type="search" placeholder="Search">
            <button type="button" class="icon-button" id="notification-button">
                <span class="material-icons">notifications</span>
                <span class="icon-button__badge" id="notification-count"><?php echo $pending_friend_requests + $pending_debts + $pending_tasks; ?></span>
            </button>
            <div id="notification-container" style="display: none;"></div>
  
            <form method="post" action="" id='log-out-button'>
                <input type="submit" name="logout" value="Logout">
            </form>

            <button class="dropdown-btn">
                <span class="material-icons">menu</span>
            </button>
        </nav>
    </header>
  -->
    <main>
      <div class="profile-tab">
        <div class="top-profile">

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
            <button onclick="showEditForm()" class="profile-button edit-button" style="background-color: #343645;">Edit Profile</button>
            <button onclick="showSettings()" class="profile-button settings-button" style="background-color: #343645;">Settings</button>
          </div>
        </div>



        <div class="list-container">
            <ul>
              <li><i class="fas fa-check-circle"></i> <a href="completed_debts.php">Completed Debts</a></li>
              <li onclick="showCompletedTasks()"><i class="fas fa-tasks"></i> <a>Completed Tasks</a></li>
              <li onclick="showPendingTasks()"><i class="fas fa-clock"></i> <a>Pending Tasks</a></li>
              <li onclick="showPendingDebts()"><i class="fas fa-money-bill-wave"></i> <a>Pending Debts</a></li>
              <li onclick="showAboutUs()"><i class="fas fa-info-circle"></i> <a>About Us</a></li>
              <li onclick="showTermsAndConditions()"><i class="fas fa-file-alt"></i> <a>Terms and Conditions</a></li>
              <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
            </ul>
        </div>
        <div class="logout-container">
          <form method="post" action="" id='log-out-button'>
              <input type="submit" name="logout" value="Logout">
          </form>
        </div>
      </div>

      <div class="content">
        <!-- Add your content on the right side of the screen here -->
        <div id="edit-form-container" style="display: none;">
          <?php
              include('edit_profile.php');
          ?>
        </div>

        <div id="settings-container" style="display: none;">
          <?php
              include('settings.php');
          ?>
        </div>

        <div id="completed-tasks-container" style="display: block;">
          <?php
              include('completed_tasks.php');
          ?>
        </div>

        <div id="pending-task-container" style="display: none;">
          <?php
              include('pending_tasks.php');
          ?>
        </div>

        <div id="pending-debts-container" style="display: none;">
          <?php
              include('pending_debts.php');
          ?>
        </div>
        <div id="about-us-container" style="display: none;">
          <?php
              include('about_us.php');
          ?>
        </div>
        <div id="terms-and-conditions-container" style="display: none;">
          <?php
              include('terms_and_conditions.php');
          ?>
        </div>
      </div>

      <i id="close-profile-tab" class="fa-regular fa-circle-xmark" href="home.php"></i>
    </main>

    <script>

      document.getElementById("close-profile-tab").addEventListener("click", function() {
        window.location.href = "home.php";
      });

      function showEditForm() {
        var formContainer = document.getElementById("completed-tasks-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("settings-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-task-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-debts-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("about-us-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("terms-and-conditions-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("edit-form-container");
        if (formContainer.style.display === "none") {
            formContainer.style.display = "block";
        } else {
            formContainer.style.display = "none";
        }
      }

      function showSettings(){
        var formContainer = document.getElementById("completed-tasks-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("edit-form-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-task-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-debts-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("about-us-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("terms-and-conditions-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("settings-container");
        if (formContainer.style.display === "none") {
            formContainer.style.display = "block";
        } else {
            formContainer.style.display = "none";
        }
      }

      function showCompletedTasks(){
        var formContainer = document.getElementById("settings-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("edit-form-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-task-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-debts-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("about-us-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("terms-and-conditions-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("completed-tasks-container");
        if (formContainer.style.display === "none") {
            formContainer.style.display = "block";
        } else {
            formContainer.style.display = "none";
        }
      }

      function showPendingTasks(){
        var formContainer = document.getElementById("settings-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("edit-form-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("completed-tasks-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-debts-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("about-us-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("terms-and-conditions-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-task-container");
        if (formContainer.style.display === "none") {
            formContainer.style.display = "block";
        } else {
            formContainer.style.display = "none";
        }
      }

      function showPendingDebts(){
        var formContainer = document.getElementById("settings-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("edit-form-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("completed-tasks-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-task-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("about-us-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("terms-and-conditions-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-debts-container");
        if (formContainer.style.display === "none") {
            formContainer.style.display = "block";
        } else {
            formContainer.style.display = "none";
        }
      }

      function showAboutUs(){
        var formContainer = document.getElementById("settings-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("edit-form-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("completed-tasks-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-task-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-debts-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("terms-and-conditions-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("about-us-container");
        if (formContainer.style.display === "none") {
            formContainer.style.display = "block";
        } else {
            formContainer.style.display = "none";
        }
      }

      function showTermsAndConditions(){
        var formContainer = document.getElementById("settings-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("edit-form-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("completed-tasks-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-task-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("pending-debts-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("about-us-container");
        if (formContainer.style.display === "block") {
            formContainer.style.display = "none";
        }
        var formContainer = document.getElementById("terms-and-conditions-container");
        if (formContainer.style.display === "none") {
            formContainer.style.display = "block";
        } else {
            formContainer.style.display = "none";
        }
      }
      
      // Toggle nav links when dropdown button is clicked
      document.querySelector('.dropdown-btn').addEventListener('click', function() {
      var navLinks = document.querySelectorAll('.nav-bar a, #search-bar, .icon-button, #log-out-button');

      for (var i = 0; i < navLinks.length; i++) {
          navLinks[i].classList.toggle('show');
      }
      });

      //*************************Notification Button Function*****************************//
      document.getElementById('notification-button').addEventListener('click', () => {
      const notificationContainer = document.getElementById('notification-container');
      if (notificationContainer.style.display === 'block') {
          notificationContainer.style.display = 'none';
          return;
      }

      notificationContainer.innerHTML = '';

      Promise.all([
          fetch('fetchFriendRequests.php').then(response => response.json()),
          fetch('fetchMyTasks.php').then(response => response.json()),
          fetch('fetchMyDebts.php').then(response => response.json())
      ])
      .then(([friendRequests, tasks, debts]) => {
          if (friendRequests.length === 0 && tasks.length === 0 && debts.length === 0) {
              notificationContainer.innerHTML = '<p>You have no notifications.</p>';
          } else {
              if (friendRequests.length > 0) {
                  const friendRequestContainer = document.createElement('div');
                  friendRequestContainer.className = "notification-section"
                  friendRequestContainer.innerHTML = '<h4 class="notification-section__title">Friend Requests</h4>';

                  friendRequests.forEach(request => {
                      friendRequestContainer.innerHTML += `<div class="notification">Friend Request From: <span>${request.sender_username}</span> </div>`;
                  });
                  
                  notificationContainer.appendChild(friendRequestContainer);
              }
              if (tasks.length > 0) {
                  const taskContainer = document.createElement('div');
                  taskContainer.className = "notification-section"
                  taskContainer.innerHTML = '<h4 class="notification-section__title">Pending Tasks</h4>';

                  tasks.forEach(task => {
                      taskContainer.innerHTML += `<div class="notification">${task.description} - Due: ${task.due_date}</div>`;
                  });
                  notificationContainer.appendChild(taskContainer);

              }
              if (debts.length > 0) {
                  const debtContainer = document.createElement('div');
                  debtContainer.className = "notification-section"
                  debtContainer.innerHTML = '<h4 class="notification-section__title">Pending Debts</h4>';
                  notificationContainer.appendChild(debtContainer);

                  debts.forEach(debt => {
                      debtContainer.innerHTML += `<div class="notification">${debt.description} - Amount: ${debt.amount} - Due: ${debt.due_date}</div>`;
                  });
                }
            }
          notificationContainer.style.display = 'block';
        });
    });
    //*************************Notification Button Function*****************************//

    </script>
  </body>
</html>