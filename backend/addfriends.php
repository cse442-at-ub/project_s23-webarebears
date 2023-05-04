<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
    require('server.php');

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

    $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
    $query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];
    $_SESSION['user_id'] = $user_id;


    $query = "SELECT Groups.group_id, Groups.group_name FROM `Group_Members` INNER JOIN `Groups` ON Group_Members.group_id = Groups.group_id WHERE Group_Members.user_id='$user_id'";
    $result = mysqli_query($db_connection, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Add Friends</title>
    <link rel="stylesheet" href="styles/addfriends.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Spartan' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <button type="button" class="icon-button" id="notification-button">
                <span class="material-icons">notifications</span>
                <span class="icon-button__badge" id="notification-count"><?php echo $pending_friend_requests + $pending_debts + $pending_tasks; ?></span>
            </button>
            <div id="notification-container" style="display: none;"></div>
            <!--
            <form method="post" action="" id='log-out-button'>
                <input type="submit" name="logout" value="Logout">
            </form>
            -->
            <button class="dropdown-btn">
                <span class="material-icons">menu</span>
            </button>
        </nav>
    </header>
<?php
    require('server.php');
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
            echo "<div class='search-result'>
                    <h3> Search Result: </h3>
                    <p>" . $user['username'] . "</p>
                    <form method='post'>
                        <input type='hidden' name='receiver_id' value='" . $user['user_id'] . "'/>
                        <button type='submit' name='send_request' value='Send Request'/>Send Friend Request</button>
                    </form>
                </div>";
        } else {
            echo "<p>No results found.</p>";
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

        $query = "UPDATE `Friend_Requests` SET status='rejected' WHERE sender_id='$sender_id' AND receiver_id='$current_user_id'";
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
    <input type="submit" value="Search" name="submit" id="submit-btn"/>
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