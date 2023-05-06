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
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Group</title>
    <link rel="stylesheet" href="styles/creategroup_style.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Spartan' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
<header>
        <nav class="nav-bar">
            <a href="profile.php">
				<img id="profile-pic" src="images/profile-temp.png" alt="Profile Icon">
			</a>
            <a href="home.php" id="home" >Home</a>
            <a id="tasksAndBalances" href="balances.php">Balances</a>
            <a id="messages" href="messages.php">Messages</a>			
            <form class="form" method="post">
                <div class="search-container">
                    <input type="text" class="login-input" name="search" placeholder="Search for friends" id="search-friend"/>
                    <button type="submit" name="submit" id="submit-btn">
                    <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!--<input id="search-bar" type="search" placeholder="Search">-->
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

<main>
    <div class="container1"></div>
    <div class="container">
        <form class="content" method="post" onsubmit="return validateForm()">
            <h2>Create a Group</h2>
            <label for="groupname">Group Name:</label>
            <input type="text" id="groupname" name="groupname">

            <div style="display: flex;align-items: center;">
                <p style="padding-right:10px;">Group Color:</p>
                <div class="color-picker-container">
                    <div class="color-picker-label" onclick="toggleColorPicker()" id="selected-color" style="width: 20px; height: 20px; background-color: #1F222A; border: 1px solid #ccc; display: inline-block;"></div>
                    <div class="color-picker-options" id="color-picker-options">
                        <div class="color-option" style="background-color: #1F222A;" onclick="setColor('#1F222A')">#</div>
                        <div class="color-option" style="background-color: #616cdb;" onclick="setColor('#616cdb')">#</div>
                        <div class="color-option" style="background-color: #f94a6e;" onclick="setColor('#f94a6e')">#</div>
                        <div class="color-option" style="background-color: #6d2fe3;" onclick="setColor('#6d2fe3')">#</div>
                    </div>
                </div>
            </div>



            <p>Members:</p>

            <select multiple size="6" style="width: 300px;" name="selected_friends[]" multiple>
                <?php
                require('server.php');
                session_start();

                if (!isset($_SESSION['username'])) {
                    header("Location: login.php");
                    exit();
                }

                $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
                $query = "SELECT friend_user_id, username FROM `friends` INNER JOIN `User Accounts` ON friends.friend_user_id = `User Accounts`.user_id WHERE friends.user_id = (SELECT user_id FROM `User Accounts` WHERE username='$username')";
                $result = mysqli_query($db_connection, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $friend_user_id = $row['friend_user_id'];
                    $friend_username = $row['username'];
                    echo "<option value='" . $friend_user_id . "'>" . $friend_username . "</option>";
                }
                ?>
            </select>
            <p1>Hold down the Ctrl (Windows) or Command (Mac) button to select multiple members.</p1>
            <div id="buttons">
                <button id="create-btn" type="submit" name="create_group" value="Create">Create</button>
                <button id="cancel-btn" type="button" value="Cancel" onclick="cancel()">Cancel</button>
            </div>
        </form>
    </div>


    <div class="container2"></div>
</main>

<script>
        function toggleColorPicker() {
            document.getElementById("color-picker-options").style.display = "block";
        }

        function setColor(color) {
            document.getElementById("selected-color").style.backgroundColor = color;
            document.getElementById("color-picker-options").style.display = "none";
        }
    
    function cancel() {
        alert("Group creation has been canceled.");
        window.location.href = "messages.php";
    }

	function validateForm() {
        const groupName = document.getElementById("groupname").value;
        const friendsList = document.querySelector("select[name='selected_friends[]']");

        if (groupName.trim() === '') {
            alert("Group name cannot be empty.");
            return false;
        }

        if (friendsList.selectedOptions.length === 0) {
            alert("You must select at least one friend.");
            return false;
        }

        return true;
    }
    // Toggle nav links when dropdown button is clicked
    document.querySelector('.dropdown-btn').addEventListener('click', function() {
    var navLinks = document.querySelectorAll('.nav-bar a, #search-container, .icon-button, #log-out-button');

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

<?php
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
                <div>
                    <p>" . $user['username'] . "</p>
                    <form method='post'>
                        <input type='hidden' name='receiver_id' value='" . $user['user_id'] . "'/>
                        <button type='submit' name='send_request' value='Send Request'>Send Friend Request</button>
                    </form>
                </div>
            </div>";
            echo '<script>
            const searchResultsContainer = document.querySelector(".search-result");

            document.addEventListener("click", (event) => {
                const target = event.target;

                // Check if the clicked element is outside the search result div
                if (!searchResultsContainer.contains(target)) {
                    searchResultsContainer.style.display = "none";
                }
            });
            </script>';
    } else {
        echo "<div class='search-result'>
                    <h3>No results found.</h3>
                </div>";
        echo '<script>
        const searchResultsContainer = document.querySelector(".search-result");

        document.addEventListener("click", (event) => {
            const target = event.target;

            // Check if the clicked element is outside the search result div
            if (!searchResultsContainer.contains(target)) {
                searchResultsContainer.style.display = "none";
            }
        });
        </script>';
    }
}

// If send friend request button is clicked, add request to Friend_Requests table
if (isset($_POST['send_request'])) {
    $receiver_id = mysqli_real_escape_string($db_connection, $_POST['receiver_id']);

    $query = "INSERT INTO `Friend_Requests` (sender_id, receiver_id, status) VALUES ('$current_user_id', '$receiver_id', 'pending')";
    mysqli_query($db_connection, $query);

    echo "<p>Friend request sent.</p>";
    
}
if (isset($_POST['create_group']) && !empty($_POST['groupname']) && !empty($_POST['selected_friends'])) {
    $group_name = mysqli_real_escape_string($db_connection, $_POST['groupname']);
    $query = "INSERT INTO `Groups` (group_name) VALUES ('$group_name')";
    mysqli_query($db_connection, $query);
    $group_id = mysqli_insert_id($db_connection);

    $selected_friends = $_POST['selected_friends'];
    $current_user_query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $current_user_result = mysqli_query($db_connection, $current_user_query);
    $current_user_id = mysqli_fetch_assoc($current_user_result)['user_id'];
    array_push($selected_friends, $current_user_id);

    foreach ($selected_friends as $friend_user_id) {
        $friend_user_id = mysqli_real_escape_string($db_connection, $friend_user_id);
        $query = "INSERT INTO `Group_Members` (group_id, user_id) VALUES ('$group_id', '$friend_user_id')";
        mysqli_query($db_connection, $query);
    }

    echo "<script>alert(\"Group '" . $group_name . "' has been created.\"); window.location.href = 'messages.php';</script>";
}
?>

</body>
</html>