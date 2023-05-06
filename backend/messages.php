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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
</head>
<body>
    <header>
        <nav class="nav-bar">
            <a href="profile.php">
            <img id="profile-pic" src="fetchProfilePicture.php" alt="Profile Icon">
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
                        echo "<p><button id='myGroup' onclick='openChat($group_id,\"$group_name\"); closeTaskForm(); closeDivideBillsForm()'>$group_name</button></p>";
                        $iteration++;
                    }
                ?>
            </div>
        </div>

        <div class="container2">
            <div class="task-and-bills">
                <button id="set-task-button" onclick="openTaskForm()"><i class="fas fa-tasks"></i></button>
                <button id="divide-bills-button" onclick="openDivideBillsForm()"><i class="fa-solid fa-comments-dollar"></i></button>
                <button id="settings-button" onclick="openSettingsForm()" style="display: none;"><i class="fa-solid fa-gear"></i></button>
            </div>
            <div id="chat-box" class="chat-box">
                <h2 id="group-chat-name"><?php echo $group_name; ?></h2>
                <div id="message-box">
                    <div id="chat-history" class="chat-history"></div>
                    <form id="message-form" class="message-form" onsubmit="return sendMessage()">
                        <input id="message-input" type="text" name="message" placeholder="Type your message...">
                        <input id="message-send-btn" type="submit" value="Send">
                    </form>
                </div>

                <div id="settings-form" style="display: none;">
                    <h2>Group Settings</h2>
                    <h3>Group Members:</h3>
                    <ul id="settings-group-members"></ul>
                    <div id="setting-buttons">
                        <button id="leave-group" onclick="leaveGroup()">Leave Group</button>
                        <button id="settings-cancel" onclick="closeSettingsForm()">Cancel</button>
                    </div>
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
                    <label for="split-type">Split Type:</label>
                    <input type="radio" id="even-split" name="split-type" value="even" checked>
                    <label for="even-split">Even</label>
                    <input type="radio" id="uneven-split" name="split-type" value="uneven">
                    <label for="uneven-split">Uneven</label><br><br>

                        <label for="bill-friend">Choose friends:</label>
                        <select id="bill-friend" name="friends[]" multiple></select><br><br>
                        <div id="uneven-split-section" style="display: none;"></div>

                        
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
                </div>
            </div>
        </div>
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
?>
    
    <script>

        let currentGroupId = null;
        let chatHistoryTimeout = null; // Add this line

        function openChat(groupId, group_name) {
            document.getElementById("group-chat-name").textContent = group_name;
            if (chatHistoryTimeout) {
                clearTimeout(chatHistoryTimeout);
            }

            if(currentGroupId != groupId){
                closeTaskForm();
                closeDivideBillsForm();
            }
            
            document.getElementById('set-task-button').style.display = 'block';
            document.getElementById('divide-bills-button').style.display = 'block';
            document.getElementById('settings-button').style.display = 'block'; 

            currentGroupId = groupId;
            document.getElementById('chat-box').style.display = 'block';
            fetchChatHistory(groupId);
            
            

            const groupName = document.querySelector(`button[id='myGroup'][onclick='openChat(${groupId}); closeTaskForm(); closeDivideBillsForm(); closeSettingsForm()']`).textContent;
            document.getElementById('group-chat-name').innerText = 'Group: ' + groupName;
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
            const settingForm = document.getElementById('settings-form');
            if (settingForm.style.display === 'block') {
                settingForm.style.display = 'none';
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

        document.getElementById('divide-bills-form').style.display = 'block';

        const messageBox = document.getElementById('message-box');
            messageBox.style.display = 'none';

        const tForm = document.getElementById('task-form');
            if (tForm.style.display === 'block') {
                tForm.style.display = 'none';
            }
        const settingForm = document.getElementById('settings-form');
            if (settingForm.style.display === 'block') {
                settingForm.style.display = 'none';
            }

        fetchGroupMembers(currentGroupId, 'bill-friend');
        const evenSplit = document.getElementById('even-split');
        evenSplit.addEventListener('change', toggleUnevenSplitSection);
        const unevenSplit = document.getElementById('uneven-split');
        unevenSplit.addEventListener('change', toggleUnevenSplitSection);
    }

    function toggleUnevenSplitSection() {
        const unevenSplitSection = document.getElementById('uneven-split-section');
        if (document.getElementById('uneven-split').checked) {
            updateFriendAmounts();
            unevenSplitSection.style.display = 'block';
        } else {
            unevenSplitSection.style.display = 'none';
        }
}

    function updateFriendAmounts() {
        const unevenSplitSection = document.getElementById('uneven-split-section');
        unevenSplitSection.innerHTML = '';

        const friendsList = document.getElementById('bill-friend');
        for (const option of friendsList.selectedOptions) {
            const friendId = option.value;
            const friendName = option.textContent;

            const label = document.createElement('label');
            label.textContent = `${friendName}: `;
            label.htmlFor = `uneven-amount-${friendId}`;

            const input = document.createElement('input');
            input.type = 'number';
            input.id = `uneven-amount-${friendId}`;
            input.name = `uneven-amount-${friendId}`;
            input.step = '0.01';
            input.min = '0.01';

            const lineBreak = document.createElement('br');

            unevenSplitSection.appendChild(label);
            unevenSplitSection.appendChild(input);
            unevenSplitSection.appendChild(lineBreak);
        }
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

        const unevenSplit = document.getElementById('uneven-split').checked;
        let unevenSplitData = null;
        if (unevenSplit) {
            unevenSplitData = {};
            let totalAmount = 0;

            const friendsList = document.getElementById('bill-friend');
            for (const option of friendsList.selectedOptions) {
                const friendId = option.value;
                const friendAmount = parseFloat(document.getElementById(`uneven-amount-${friendId}`).value);

                if (isNaN(friendAmount) || friendAmount <= 0) {
                    alert('Please enter a valid amount for each selected friend.');
                    return false;
                }

                unevenSplitData[friendId] = friendAmount;
                totalAmount += friendAmount;
            }

            if (Math.abs(totalAmount - amount) > 0.01) {
                alert('The total amount for the uneven split does not match the total bill amount.');
                return false;
}

        }

        const formData = new FormData();
        formData.append('group_id', currentGroupId);
        formData.append('friends', JSON.stringify(friends));
        formData.append('description', description);
        formData.append('amount', amount);
        formData.append('due_date', dueDate);
        formData.append('uneven_split', unevenSplit);

        if (unevenSplitData) {
            formData.append('uneven_split_data', JSON.stringify(unevenSplitData));
        }

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

        document.getElementById('bill-friend').addEventListener('change', function() {
            if (document.getElementById('uneven-split').checked) {
                updateFriendAmounts();
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

//*************************Group Setting Function*****************************//
function openSettingsForm() {
    if (currentGroupId === null) {
        alert('Please select a group chat before viewing settings.');
        return;
    }
    const messageBox = document.getElementById('message-box');
    messageBox.style.display = 'none';

    const taskForm = document.getElementById('task-form');
    if (taskForm.style.display === 'block') {
        taskForm.style.display = 'none';
    }

    const divideBillsForm = document.getElementById('divide-bills-form');
    if (divideBillsForm.style.display === 'block') {
        divideBillsForm.style.display = 'none';
    }
    document.getElementById('settings-form').style.display = 'block';
    fetchGroupMembersSettings(currentGroupId, 'settings-group-members', true);
}

function closeSettingsForm() {
    document.getElementById('settings-form').style.display = 'none';

    const messageBox = document.getElementById('message-box');
            if (messageBox.style.display !== 'block') {
                messageBox.style.display = 'block';
            }
}

function fetchGroupMembersSettings(groupId, selectId = 'task-friend', readOnly = false) {
    fetch(`fetchGroupMembers.php?group_id=${groupId}`)
        .then(response => response.json())
        .then(members => {
            const fList = document.getElementById(selectId);
            fList.innerHTML = '';

            // Add yourself to the roster
            const currentUser = {
                username: "<?php echo $_SESSION['username']; ?>",
                user_id: "<?php echo $_SESSION['user_id']; ?>"
            };
            members.push(currentUser);

            members.forEach(member => {
                const listNode = document.createElement('li');
                listNode.textContent = member.username;
                if (!readOnly) {
                    const labelNode = document.createElement('label');
                    labelNode.className = 'custom-checkbox';

                    const inputNode = document.createElement('input');
                    inputNode.type = 'checkbox';
                    inputNode.value = member.user_id;

                    const spanNode = document.createElement('span');
                    spanNode.className = 'checkmark'

                    labelNode.appendChild(inputNode);
                    labelNode.appendChild(spanNode);
                    listNode.appendChild(labelNode);
                }
                fList.appendChild(listNode);
            });
        });
}


function leaveGroup() {
    if (!confirm("Are you sure you want to leave this group?")) {
        return;
    }

    const formData = new FormData();
    formData.append('group_id', currentGroupId);

    fetch('leaveGroup.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(responseText => {
        console.log('Response from leaveGroup.php:', responseText);
        closeSettingsForm();
        location.reload();
    });
}
//*************************Group Setting Function*****************************//

    </script>
</body>
</html>