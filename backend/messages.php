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
</head>
<body>
    <header style="display: flex;
justify-content: space-between;
align-items: center;
">
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

    <div class="main-wrapper">
    <main>
        <!-- Main content goes here -->
        <div id="options">
            <button id="addFriends" onclick="window.location.href='addfriends.php'" >Add Friends</button>
            <button id="createGroups" onclick="window.location.href='creategroup.php'" >Create Group</button>
        </div>

        <h id="myGroup-text">My Groups:</h>
        <myGroup>
            <?php
                $iteration = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    $group_id = $row['group_id'];
                    $group_name = $row['group_name'];
                    echo "<p><button id='myGroup' onclick='openChat($group_id); closeTaskForm(); closeDivideBillsForm()'>$group_name</button></p>";
                    $iteration++;
                }
            ?>
        </myGroup>
    
    <div class = "container">
        <button id="set-task-button" onclick="openTaskForm(); closeDivideBillsForm()">Set Task</button>
        <button id="divide-bills-button" onclick="openDivideBillsForm(); closeTaskForm()">Divide Bills</button>
        <div id="chat-box" class="chat-box">
            <div id="chat-history" class="chat-history"></div>
            <form id="message-form" class="message-form" onsubmit="return sendMessage()">
                <input id="message-input" type="text" name="message" placeholder="Type your message...">
                <input id="message-send-btn" type="submit" value="Send">
            </form>
        </div>
    </div>

    <div id="task-form" style="display: none;">
    <h2>Set Task</h2>
    <form id="create-task-form" onsubmit="return createTask()">
        <label for="task-friend">Choose a friend:</label>
        <select id="task-friend" name="friend"></select><br><br>
        
        <label for="task-description" id="task-description-text">Task Description:</label>
        <textarea id="task-description" name="description" rows="4" cols="50" required></textarea><br><br>

        <label for="task-due-date" id="due-data-text">Due Date:</label>
        <input id="task-due-date" type="date" name="due_date" required><br><br>

        <input type="submit" value="Create Task" id="create-task">

        <button id="task-cancel" onclick="closeTaskForm()">Cancel</button>
    </form>
    </div>

    <div id="divide-bills-form" style="display: none;">
        <h2>Divide Bills</h2>
        <form id="create-bill-form" onsubmit="return divideBill()">
            <label for="bill-friend">Choose friends:</label>
            <select id="bill-friend" name="friends[]" multiple></select><br><br>
            
            <label for="bill-description" id="bill-description-text">Bill Description:</label>
            <textarea id="bill-description" name="description" rows="4" cols="50" required></textarea><br><br>

            <label for="bill-amount" id="bill-amount-text">Total Amount:</label>
            <input id="bill-amount" type="number" name="amount" step="0.01" min="0.01" required><br><br>

            <label for="bill-due-date" id="due-data-text">Due Date:</label>
            <input id="bill-due-date" type="date" name="due_date" required><br><br>

            <input type="submit" value="Divide Bill" id="divide-bill">

            <button id="bill-cancel" onclick="closeDivideBillsForm()">Cancel</button>
        </form>
    </div>

    </main>
    </div>

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
            document.getElementById('task-form').style.display = 'block';
            fetchGroupMembers(currentGroupId);
        }

        function closeTaskForm() {
            document.getElementById('task-form').style.display = 'none';
        }

        function fetchGroupMembers(groupId) {
            fetch(`fetchGroupMembers.php?group_id=${groupId}`)
                .then(response => response.json())
                .then(members => {
                    const select = document.getElementById('task-friend');
                    select.innerHTML = '';
                    members.forEach(member => {
                        const option = document.createElement('option');
                        option.value = member.user_id;
                        option.textContent = member.username;
                        select.appendChild(option);
                    });
                });
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
            document.getElementById('divide-bills-form').style.display = 'block';
            fetchGroupMembers(currentGroupId, 'bill-friend');
        }

        function closeDivideBillsForm() {
            document.getElementById('divide-bills-form').style.display = 'none';
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





    </script>

</body>
</html>