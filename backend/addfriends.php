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
        <nav class="nav-bar">
            <a href="profile.php">
				<img id="profile-pic" src="images/profile-temp.png" alt="Profile Icon">
			</a>
            <a href="home.php" id="home" >Home</a>
            <a id="tasksAndBalances" href="Balances.php">Balances</a>
            <a id="messages" href="messages.php">Messages</a>			

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
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $current_user_id = $_SESSION['user_id'];

    // If form is submitted, search for user
    if (isset($_POST['submit'])) {
        $search_term = mysqli_real_escape_string($db_connection, $_POST['search']);
        $query = "SELECT * FROM `User Accounts` WHERE username='$search_term'";
        $result = mysqli_query($db_connection, $query);

        // If user found, display user's information and send friend request button
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $receiver_id = $user['user_id'];
            $query = "INSERT INTO `Friend_Requests` (sender_id, receiver_id) VALUES ('$current_user_id', '$receiver_id')";
            $result = mysqli_query($db_connection, $query);
            if ($result) {
                echo "<p id='search-status'>Friend request sent to " . $user['username'] . "!</p>";
            } else {
                echo "<p id='search-status'>Failed to send friend request.</p>";
            }
        } else {
            echo "<p id='user-not-found'>User not found.</p>";
        }
        
    }

    // If send friend request button is clicked, add request to Friend_Requests table
    if (isset($_POST['send_request'])) {
        $receiver_id = mysqli_real_escape_string($db_connection, $_POST['receiver_id']);

        $query = "INSERT INTO `Friend_Requests` (sender_id, receiver_id, status) VALUES ('$current_user_id', '$receiver_id', 'pending')";
        mysqli_query($db_connection, $query);

        echo "<p>Friend request sent.</p>";
    }

    // If accept friend request button is clicked, update Friend_Requests table and insert records in Friends table
    if (isset($_POST['accept_request'])) {
        $sender_id = mysqli_real_escape_string($db_connection, $_POST['sender_id']);

        // Update Friend_Requests table
        $query1 = "UPDATE `Friend_Requests` SET status='accepted' WHERE sender_id='$sender_id' AND receiver_id='$current_user_id'";
        mysqli_query($db_connection, $query1);

        // Add records to Friends table
        $query2 = "INSERT INTO `friends` (user_id, friend_user_id) VALUES ('$current_user_id', '$sender_id')";
        mysqli_query($db_connection, $query2);

        $query3 = "INSERT INTO `friends` (user_id, friend_user_id) VALUES ('$sender_id', '$current_user_id')";
        mysqli_query($db_connection, $query3);

        echo "<p>Friend request accepted.</p>";
    }
    // If decline friend request button is clicked, update Friend_Requests table
    if (isset($_POST['decline_request'])) {
        $sender_id = mysqli_real_escape_string($db_connection, $_POST['sender_id']);

        $query = "UPDATE `Friend_Requests` SET status='declined' WHERE sender_id='$sender_id' AND receiver_id='$current_user_id'";
        mysqli_query($db_connection, $query);

        echo "<p>Friend request declined.</p>";
    }

    // Display the list of friend requests
    echo "<div class='friend-requests-container'>";
    echo "<h3>Friend Requests</h3>";
    echo "<div class='friend-requests'>";
    $query = "SELECT `User Accounts`.user_id, `User Accounts`.username FROM `Friend_Requests` INNER JOIN `User Accounts` ON `Friend_Requests`.sender_id = `User Accounts`.user_id WHERE `Friend_Requests`.receiver_id = '$current_user_id' AND `Friend_Requests`.status = 'pending'";
    $result = mysqli_query($db_connection, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $sender_id = $row['user_id'];
        $sender_username = $row['username'];
        echo "<div>
                <p>" . $sender_username . "</p>
                <form method='post' class='accept-request-form'>
                    <input type='hidden' name='sender_id' value='" . $sender_id . "'>
                    <button type='submit' name='accept_request' value='Accept'/>Accept</button>
                </form>
                <form method='post' class='decline-request-form'>
                    <input type='hidden' name='sender_id' value='" . $sender_id . "'>
                    <button type='submit' name='decline_request' value='Decline'/>Decline</button>
                </form>
            </div>";
    }
    echo "</div>";
    echo "</div>";

    // Display the list of friends
    echo "<div class='friends_list_container'>";
    echo "<h3 id='friends-title'>Friends</h3>";
    echo "<div class='friends_list'>";
    $query = "SELECT `User Accounts`.user_id, `User Accounts`.username FROM `friends` INNER JOIN `User Accounts` ON `friends`.friend_user_id = `User Accounts`.user_id WHERE `friends`.user_id = '$current_user_id'";
    $result = mysqli_query($db_connection, $query);

    echo "<ul class='friends-list' style='padding-left: 20px; padding-right: 20px';>";
    while ($row = mysqli_fetch_assoc($result)) {
        $friend_user_id = $row['user_id'];
        $friend_username = $row['username'];
        echo "<p>" . $friend_username . "</p>";
    }
    echo "</ul>";
    echo "</div>";
    echo "</div>"
?>
    
<form class="form" method="post">
    <input type="text" class="login-input" name="search" placeholder="Search for friends" id="search-friend"/>
    <input type="submit" value="Send Request" name="submit" id="submit-btn"/>
</form>
    
<!-- <button id="cancel-btn" type="button" value="Cancel" onclick="cancel()">Cancel</button>-->

<script>
    function cancel() {
        window.location.href = "messages.php";
    }

    const navbar = document.querySelector('.nav-bar');
        const searchbar = document.querySelector('#search-bar');
        const notifications = document.querySelector('.icon-button');
        const logoutButton = document.querySelector('#log-out-button');

        let lastScrollTop = 0;

        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop) {
                navbar.style.top = '-70px';
            } else {
                navbar.style.top = '0';
            }
            lastScrollTop = scrollTop;
        });

        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 50) {
                searchbar.style.visibility = 'hidden';
                notifications.style.visibility = 'hidden';
                navbar.style.visibility = "hidden";
                logoutButton.style.visibility = "hidden";
            } else {
                searchbar.style.visibility = 'visible';
                notifications.style.visibility = 'visible';
                navbar.style.visibility = 'visible';
                logoutButton.style.visibility = "visible";
            }
        });
</script>

</body>
</html>
