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
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles/home_style.css"/>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body id="homepage">
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

	<main id="grid2">
        <recent>
            <u style="color: white; font-family: sans-serif; font-weight: 450; font-size: larger; margin-left: 20px">Recent</u>
            <div id="recent">Recent</div>
			
        </recent>
        <tasks>
            <h3 style="color: white; font-size: larger; font-weight: 400;">Your Tasks:</h3>
            <p style="color: white;"> Select Tasks that you have finished!</p>
            <div id="tasks">Your Tasks: 
				<p id="tasks-direction"> Select Tasks that you have finished!</p>
				</div>
			</div>
            <p></p>
            <button id="complete-tasks-btn" onclick="completeTasks()">Complete</button>		
        </tasks>
       
	</main>

    <script>
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

                    const groupName = document.createElement('h4');
                    groupName.textContent = 'Group: ' + message.group_name;
                    recentMessage.appendChild(groupName);

                    const messageContent = document.createElement('p');
                    messageContent.textContent = message.message_content;
                    recentMessage.appendChild(messageContent);

                    const sender = document.createElement('p');
                    sender.textContent = 'Sent by: ' + message.sender;
                    recentMessage.appendChild(sender);
                });

                })
                .catch(error => {
                    console.error('Error fetching recent messages:', error);
                });
        }
        
        const searchBar = document.getElementById('search-bar');
        const searchResultsContainer = document.createElement('div');
        searchResultsContainer.classList.add('search-results');
        searchBar.insertAdjacentElement('afterend', searchResultsContainer);

        searchBar.addEventListener('input', (event) => {
            const query = event.target.value;

            if (query.trim() === '') {
                searchResultsContainer.innerHTML = '';
                return;
            }

            fetch(`search.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error searching:', data.error);
                        return;
                    }

                    searchResultsContainer.innerHTML = '';

                    data.results.forEach(result => {
                        const resultItem = document.createElement('div');
                        resultItem.textContent = `${result.type}: ${result.result}`;
                        searchResultsContainer.appendChild(resultItem);
                    });
                })
                .catch(error => {
                    console.error('Error searching:', error);
                });
        });

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

                        const taskTitle = document.createElement('h4');
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
    </script>
</body>
</html>
