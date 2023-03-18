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
			<a id="tasksAndBalances" href="tasksAndBalances.html">Tasks and Balances</a>
			<a id="messages" href="messages.html">Messages</a>			
		</nav>
		<nav class="nav-right">
			<input id="search-bar" type="search" placeholder="Search">
			<button type="button" class="icon-button">
				<span class="material-icons">notifications</span>
				<span class="icon-button__badge">2</span>
			</button>
		</nav>
		
	</header>

	<main id="grid2">
        <recent>
            <u id="recent">Recent</u>
			<div id="notification">
				<?php
					$notifications = array("Junstin just gave Laurence a 'Do Laundry' Task!", "Notification2", "Notification3");
					foreach ($notifications as $notifcation) {
						echo '<div id="notification-text">' . $notifcation . '</div>';
					}
				?>
			</div>
        </recent>
        <tasks>
            <div id="tasks">Your Tasks: 
				<p id="tasks-direction"> Select Tasks that you have finished!</p>
				<?php
					$tasks = array("Task 1", "Task 2", "Task 3", "Task4", "Task5");
					foreach ($tasks as $task) {
						echo '<div class="task">' . $task . '</div>';
					}
				?>
				<div class=complete-container>
					<button class="complete-button">Complete</button>
				</div>
				<script>
					let tasks = document.querySelectorAll('.task');
					let completeButton = document.querySelector('.complete-button');

					tasks.forEach(task => {
						task.addEventListener('click', () => {
							task.classList.toggle('selected');
							let selectedTasks = document.querySelectorAll('.selected');
							if (selectedTasks.length > 0) {
								completeButton.style.display = 'block';
							} else {
								completeButton.style.display = 'none';
							}
						});
					});

					completeButton.addEventListener('click', () => {
						let selectedTasks = document.querySelectorAll('.selected');
						selectedTasks.forEach(task => {
							task.remove();
						});
						completeButton.style.display = 'none';
					});
				</script>
				</div>
				
			</div>
			
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
                        const groupName = document.createElement('h4');
                        groupName.textContent = message.group_name;
                        recentMessagesContainer.appendChild(groupName);

                        const messageContent = document.createElement('p');
                        messageContent.textContent = message.message_content;
                        recentMessagesContainer.appendChild(messageContent);

                        const sender = document.createElement('p');
                        sender.textContent = 'Sent by: ' + message.sender;
                        recentMessagesContainer.appendChild(sender);
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
                        const checkBox = document.createElement('input');
                        checkBox.type = 'checkbox';
                        checkBox.setAttribute('data-task-id', task.task_id);
                        taskItem.appendChild(checkBox);

                        const description = document.createElement('span');
                        description.textContent = task.description;
                        taskItem.appendChild(description);

                        tasksContainer.appendChild(taskItem);
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
