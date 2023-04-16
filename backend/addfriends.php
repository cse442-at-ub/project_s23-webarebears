<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Add Friends</title>
    <link rel="stylesheet" href="styles/addfriends.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
<?php
    require('server.php');
    session_start();
    // Check if user is logged in

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    // If form is submitted, search for user
    if (isset($_POST['submit'])) {
        $search_term = mysqli_real_escape_string($db_connection, $_POST['search']);
        $query = "SELECT * FROM `User Accounts` WHERE username='$search_term'";
        $result = mysqli_query($db_connection, $query);

        // If user found, display user's information and add friend button
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            echo "<div class='search-result'>
                    <img src='" . $user['profile_picture'] . "'/>
                    <h3> Search Result: </h3>
                    <h2>" . $user['name'] . "</h2>
                    <p>" . $user['username'] . "</p>
                    <form method='post'>
                        <input type='hidden' name='friend_username' value='" . $user['username'] . "'/>
                        <button type='submit' name='add_friend' value='Add Friend'/>Add Friend</button>
                    </form>
                </div>";
        } else {
            echo "<p>No results found.</p>";
        }
    }

    // If add friend button is clicked, add user to current user's friends list
    if (isset($_POST['add_friend'])) {
        $friend_username = mysqli_real_escape_string($db_connection, $_POST['friend_username']);
        $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
        
        $query1 = "INSERT INTO `Friends` (username, friend_username) VALUES ('$username', '$friend_username')";
        mysqli_query($db_connection, $query1);
        
        $query2 = "INSERT INTO `Friends` (username, friend_username) VALUES ('$friend_username', '$username')";
        mysqli_query($db_connection, $query2);
        
        echo "<p>" . $friend_username . " has been added as a friend.</p>";
    }

    // If remove friend button is clicked, remove friend from current user's friends list
    if (isset($_POST['remove_friend'])) {
        $friend_username = mysqli_real_escape_string($db_connection, $_POST['friend_username']);
        $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
        
        $query1 = "DELETE FROM `Friends` WHERE username='$username' AND friend_username='$friend_username'";
        mysqli_query($db_connection, $query1);
        
        $query2 = "DELETE FROM `Friends` WHERE username='$friend_username' AND friend_username='$username'";
        mysqli_query($db_connection, $query2);
        
        echo "<p>" . $friend_username . " has been removed as a friend.</p>";
    }
        // Query the database for the current user's friends
        $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
        $query = "SELECT friend_username FROM `Friends` WHERE username='$username'";
        $result = mysqli_query($db_connection, $query);
    
        // Display the list of friends, with a remove friend button next to each friend
        echo "<div class=friends_list>";
        echo "<h3>Friends List</h3>";
        echo "<ul class='friends-list'>";
        while ($row = mysqli_fetch_assoc($result)) {
            $friend_username = $row['friend_username'];
            echo "<div>" . $friend_username . " 
                      <form method='post' class='remove-friend-form'>
                          <input type='hidden' name='friend_username' value='" . $friend_username . "'>
                          <button type='submit' name='remove_friend' value='Remove'/>Remove</button>
                      </form>
                  </div>";
        }
        echo "</ul>";
        echo "</div>"
    ?>
    
    <form class="form" method="post">
        <h3 class="login-title">Search Friends</h3>
        <input type="text" class="login-input" name="search" placeholder="Search for friends" id="search-friend"/>
        <input type="submit" value="Search" name="submit" id="submit-btn"/>
    </form>
    
    <button id="cancel-btn" type="button" value="Cancel" onclick="cancel()">Cancel</button>

    <script>
        function cancel() {
        window.location.href = "messages.php";
    }
    </script>

    </body>
    </html>
    