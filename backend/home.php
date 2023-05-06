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
    
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles/home_style.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito&display=swap" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Spartan' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body id="homepage">
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


<?php
    require('server.php');

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
?>

    <main id="grid2">
        <div class="recent">
            <p1>Recent</p1>
            <recent id="recent"></recent>
        </div>
        <div class="your_task_container">
            <div class="your_task">
                <h3 style="font-family: 'Spartan';font-style: normal;font-weight: 900;font-size: 20px;">Your Tasks:
                    <span style="font-family: 'Spartan';font-style: normal;font-weight: 900;font-size: 15px;line-height: 18px;padding-left: 15%;"> Select Tasks that you have finished!</span>
                </h3>

                <div id="tasks"></div>
                <div id="space"></div>
                <button id="complete-tasks-btn" onclick="completeTasks()" style="text-align: center;">Complete</button>
        </div>
       
    </main>

    <script>
        
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


        function fetchRecentMessages() {
            fetch('fetchRecentMessages.php')
                .then(response => response.json())
                .then(messages => {
                    console.log(messages); // Add this line to inspect the content of the response

                    const recentMessagesContainer = document.getElementById('recent');
                    recentMessagesContainer.innerHTML = '';

                    messages.forEach(message => {
                    const recentMessage = document.createElement('div');
                    recentMessage.classList.add('recent-message');
                    recentMessagesContainer.appendChild(recentMessage);

                    const groupName = document.createElement('p');
                    groupName.textContent = 'Group: ' + message.group_name;
                    recentMessage.appendChild(groupName);

                    const messageContent = document.createElement('p');
                    messageContent.textContent = message.message_content;
                    recentMessage.appendChild(messageContent);

                    const sender = document.createElement('p');
                    sender.innerHTML = 'Sent by: <u>' + message.sender + '</u>';
                    recentMessage.appendChild(sender);
                });

                })
                .catch(error => {
                    console.error('Error fetching recent messages:', error);
                });
        }

        function fetchMyTasks() {
            fetch('fetchMyTasks.php')
                .then(response => response.json())
                .then(tasks => {
                    const tasksContainer = document.getElementById('tasks');
                    tasksContainer.innerHTML = '';

                    tasks.forEach(task => {
                        const taskItem = document.createElement('div');
                        taskItem.classList.add('task'); // Add this line to apply the task class
                        tasksContainer.appendChild(taskItem);

                        const taskTitle = document.createElement('p');
                        taskTitle.textContent = task.description;
                        taskItem.appendChild(taskTitle);
                        const checkBox = document.createElement('input');
                        checkBox.type = 'checkbox';
                        checkBox.setAttribute('data-task-id', task.task_id);
                        taskItem.appendChild(checkBox);

                        const description = document.createElement('span');
                        description.textContent = ' - Due: ' + task.due_date; // Assuming there's a due_date property in the task object
                        taskItem.appendChild(description);
                    });
                })
                .catch(error => {
                    console.error('Error fetching tasks:', error);
                });
        }


        function completeTasks() {
            const tasksContainer = document.getElementById('tasks');
            const checkBoxes = tasksContainer.querySelectorAll('input[type=checkbox]:checked');
            const taskIds = [];

            checkBoxes.forEach(checkBox => {
                taskIds.push(checkBox.getAttribute('data-task-id'));
            });

            if (taskIds.length === 0) {
                alert('Please select at least one task to mark as complete.');
                return;
            }

            fetch('completeTasks.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ taskIds }),
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        fetchMyTasks();
                    } else {
                        console.error('Error completing tasks:', result.error);
                    }
                })
                .catch(error => {
                    console.error('Error completing tasks:', error);
                });
        }

        fetchRecentMessages();
        fetchMyTasks();

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