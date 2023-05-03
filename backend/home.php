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
            <a id="tasksAndBalances" href="Balances.php">Balances</a>
            <a id="messages" href="messages.php">Messages</a>			

            <input id="search-bar" type="search" placeholder="Search">
            <button type="button" class="icon-button">
                <span class="material-icons">notifications</span>
                <span class="icon-button__badge"></span>
            </button>
            <form method="post" action="" id='log-out-button'>
                <input type="submit" name="logout" value="Logout">
            </form>
            <button class="dropdown-btn">
                <span class="material-icons">menu</span>
            </button>
        </nav>
    </header>


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
                <button id="complete-tasks-btn" onclick="completeTasks()" style="text-align: center;">Complete</button>
        </div>
       
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

    // Toggle nav links when dropdown button is clicked
    document.querySelector('.dropdown-btn').addEventListener('click', function() {
        var navLinks = document.querySelectorAll('.nav-bar a, #search-bar, .icon-button, #log-out-button');

    for (var i = 0; i < navLinks.length; i++) {
        navLinks[i].classList.toggle('show');
    }
    });
    </script>
</body>
</html>
