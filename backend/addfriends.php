<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Add Friends</title>
    <link rel="stylesheet" href="styles/addfriends.css"/>
</head>
<body>
<header>
	<nav>
		<a href="home.html">Home</a>
		<a href="tasksAndBalances.html">Tasks and Balances</a>
		<a href="messages.html">Messages</a>
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
                    <h2>" . $user['name'] . "</h2>
                    <p>" . $user['username'] . "</p>
                    <form method='post'>
                        <input type='hidden' name='friend_username' value='" . $user['username'] . "'/>
                        <input type='submit' name='add_friend' value='Add Friend'/>
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
        echo "<h1>Friends List</h1>";
        echo "<ul class='friends-list'>";
        while ($row = mysqli_fetch_assoc($result)) {
            $friend_username = $row['friend_username'];
            echo "<li>" . $friend_username . " 
                      <form method='post' class='remove-friend-form'>
                          <input type='hidden' name='friend_username' value='" . $friend_username . "'/>
                          <input type='submit' name='remove_friend' value='Remove'/>
                      </form>
                  </li>";
        }
        echo "</ul>";
    ?>
    
    <form class="form" method="post">
        <h1 class="login-title">Add Friends</h1>
        <input type="text" class="login-input" name="search" placeholder="Search for friends"/>
        <input type="submit" value="Search" name="submit" class="login-button"/>
    </form>
    
    </body>
    </html>
    