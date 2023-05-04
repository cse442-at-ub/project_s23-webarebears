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
    <meta charset="utf-8">
    <title>My Messages</title>
    <link rel="stylesheet" href="styles/messages_style.css"/>
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

    <main>
        <!-- Main content goes here -->
        <div class="container1">
            <div id="options">
                <button id="addFriends" onclick="window.location.href='addfriends.php'" >Add Friends</button>
                <button id="createGroups" onclick="window.location.href='creategroup.php'" >Create Group</button>
            </div>
            
            
            <div class="group_list">
                <?php
                    $iteration = 0;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $group_id = $row['group_id'];
                        $group_name = $row['group_name'];
                        echo "<p><button id='myGroup' onclick='openChat($group_id); closeTaskForm(); closeDivideBillsForm()'>$group_name</button></p>";
                        $iteration++;
                    }
                ?>
            </div>
        </div>

        <div class="container2">
            <div class="task-and-bills">
                <button id="set-task-button" onclick="openTaskForm()">Set Task</button>
                <button id="divide-bills-button" onclick="openDivideBillsForm()">Divide Bills</button>
            </div>
            <div id="chat-box" class="chat-box">
                <div id="message-box">
                    <div id="chat-history" class="chat-history"></div>
                    <form id="message-form" class="message-form" onsubmit="return sendMessage()">
                        <input id="message-input" type="text" name="message" placeholder="Type your message...">
                        <input id="message-send-btn" type="submit" value="Send">
                    </form>
                </div>

            
                <div id="task-form" style="display: none;">
                    <h2>Set Task</h2>
                    <form id="create-task-form" onsubmit="return createTask()">
                        <label for="task-friend">Choose a friend:</label>
                        <select id="task-friend" name="friends[]" multiple></select><br>
                        
                        <label for="task-description" id="task-description-text">Task Description:</label>
                        <textarea id="task-description" name="description" rows="4" cols="50" required></textarea><br>

                        <label for="task-due-date" id="due-data-text">Due Date:</label>
                        <input id="task-due-date" type="date" name="due_date" required><br>
                        <div id="task-buttons">
                            <input type="submit" value="Create Task" id="create-task">
                            <button id="task-cancel" onclick="closeTaskForm()">Cancel</button>
                        </div>
                    </form>
                </div>

                <div id="divide-bills-form" style="display: none;">
                    <h2>Divide Bills</h2>
                    <form id="create-bill-form" onsubmit="return divideBill()">
                        <label for="bill-friend">Choose friends:</label>
                        <select id="bill-friend" name="friends[]" multiple></select><br>
                        
                        <label for="bill-description" id="bill-description-text">Bill Description:</label>
                        <textarea id="bill-description" name="description" rows="4" cols="50" required></textarea><br>

                        <label for="bill-amount" id="bill-amount-text">Total Amount:</label>
                        <input id="bill-amount" type="number" name="amount" step="0.01" min="0.01" required><br><br>

                        <label for="bill-due-date" id="due-data-text">Due Date:</label>
                        <input id="bill-due-date" type="date" name="due_date" required><br>
                        <div id="bills-buttons">
                            <input type="submit" value="Divide Bill" id="divide-bill">
                            <button id="bills-cancel" onclick="closeDivideBillsForm()">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>

        let currentGroupId = null;
        let chatHistoryTimeout = null; // Add this line

        function openChat(groupId) {
            if (chatHistoryTimeout) { // Add this block
                clearTimeout(chatHistoryTimeout);
            }
            currentGroupId = groupId;
            document.getElementById('chat-box').style.display = 'block';
            fetchChatHistory(groupId);
            document.getElementById('set-task-button').style.display = 'block';
            document.getElementById('divide-bills-button').style.display = 'block';
        }

        function fetchChatHistory(groupId, skipTimeout) {
            fetch(`fetchChatHistory.php?group_id=${groupId}`)
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        throw new Error(`Error fetching chat history. Status: ${response.status}`);
                    }
                })
                .then(chatHistoryHtml => {
                    console.log("Fetched chat history:", chatHistoryHtml);
                    document.getElementById('chat-history').innerHTML = chatHistoryHtml;
                })
                .catch(error => {
                    console.error("Error fetching chat history:", error);
                });

            if (!skipTimeout) {
                chatHistoryTimeout = setTimeout(() => fetchChatHistory(groupId), 5000);
            }
        }

        function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();
            if (message === '') {
                return false;
            }

            const formData = new FormData();
            formData.append('group_id', currentGroupId);
            formData.append('message', message);

            fetch('sendMessage.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Add this line to retrieve the response text
            .then(responseText => { // Modify this line to process the response text
                console.log('Response from sendMessage.php:', responseText); // Log the response text to the console
                fetchChatHistory(currentGroupId, true); // Add true as the second argument
                messageInput.value = '';
            });

            return false;
        }


        function openTaskForm() {
            if (currentGroupId === null) {
                alert('Please select a group chat before setting a task.');
                return;
            }

            const messageBox = document.getElementById('message-box');
            messageBox.style.display = 'none';

            const divideBillsForm = document.getElementById('divide-bills-form');
            if (divideBillsForm.style.display === 'block') {
                divideBillsForm.style.display = 'none';
            }

            const taskForm = document.getElementById('task-form');
            taskForm.style.display = 'block';

            fetchGroupMembers(currentGroupId);
        }

        
        function closeTaskForm() {
            const taskForm = document.getElementById('task-form');
            taskForm.style.display = 'none';

            const messageBox = document.getElementById('message-box');
            if (messageBox.style.display !== 'block') {
                messageBox.style.display = 'block';
            }
        }

        function createTask() {
            const friend = document.getElementById('task-friend').value;
            const description = document.getElementById('task-description').value;
            const dueDate = document.getElementById('task-due-date').value;

            if (!friend || !description || !dueDate) {
                alert('Please fill in all fields.');
                return false;
            }

            const formData = new FormData();
            formData.append('group_id', currentGroupId);
            formData.append('friend', friend);
            formData.append('description', description);
            formData.append('due_date', dueDate);

            fetch('createTask.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(responseText => {
                console.log('Response from createTask.php:', responseText);
                closeTaskForm();
            });

            return false;
        }


        function fetchGroupMembers(groupId, selectId = 'task-friend') {
            fetch(`fetchGroupMembers.php?group_id=${groupId}`)
                .then(response => response.json())
                .then(members => {
                    const select = document.getElementById(selectId);
                    select.innerHTML = '';
                    members.forEach(member => {
                        const option = document.createElement('option');
                        option.value = member.user_id;
                        option.textContent = member.username;
                        select.appendChild(option);
                    });
                });
        }

        function openDivideBillsForm() {
            if (currentGroupId === null) {
                alert('Please select a group chat before dividing a bill.');
                return;
            }

            const messageBox = document.getElementById('message-box');
            messageBox.style.display = 'none';

            const taskForm = document.getElementById('task-form');
            if (taskForm.style.display === 'block') {
                taskForm.style.display = 'none';
            }

            const divideBillsForm = document.getElementById('divide-bills-form');
            divideBillsForm.style.display = 'block';

            fetchGroupMembers(currentGroupId, 'bill-friend');
        }



        function closeDivideBillsForm() {
            const divideBillsForm = document.getElementById('divide-bills-form');
            divideBillsForm.style.display = 'none';

            const messageBox = document.getElementById('message-box');
            if (messageBox.style.display !== 'block') {
                messageBox.style.display = 'block';
            }
        }

        function divideBill() {
            const friends = Array.from(document.getElementById('bill-friend').selectedOptions).map(option => option.value);
            const description = document.getElementById('bill-description').value;
            const amount = document.getElementById('bill-amount').value;
            const dueDate = document.getElementById('bill-due-date').value;

            if (friends.length === 0 || !description || !amount || !dueDate) {
                alert('Please fill in all fields.');
                return false;
            }

            const formData = new FormData();
            formData.append('group_id', currentGroupId);
            formData.append('friends', JSON.stringify(friends));
            formData.append('description', description);
            formData.append('amount', amount);
            formData.append('due_date', dueDate);

            fetch('divideBill.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(responseText => {
                console.log('Response from divideBill.php:', responseText);
                closeDivideBillsForm();
            });        
        return false;
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