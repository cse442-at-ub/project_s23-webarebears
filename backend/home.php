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
    <style>
        /* CSS styles go here */
    </style>
</head>

<body id="homepage">
    <header>
        <nav>
            <a href="home.php">Home</a>
            <a href="tasksAndBalances.php">Tasks and Balances</a>
            <a href="messages.php">Messages</a>
            <form method="post" action="">
                <input type="submit" name="logout" value="Logout">
            </form>
        </nav>
    </header>

    <main>
        <h2>Recent Messages</h2>
        <div id="recent-messages">
            <!-- Recent messages will be displayed here -->
        </div>

        <h2>My Tasks</h2>
        <div id="my-tasks">
            <!-- My tasks will be displayed here -->
        </div>
        <button id="complete-tasks-btn" onclick="completeTasks()">Complete</button>
    </main>

    <script>
        function fetchRecentMessages() {
            fetch('fetchRecentMessages.php')
                .then(response => response.json())
                .then(messages => {
                    console.log(messages); // Add this line to inspect the content of the response

                    const recentMessagesContainer = document.getElementById('recent-messages');
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
                    const tasksContainer = document.getElementById('my-tasks');
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
            const tasksContainer = document.getElementById('my-tasks');
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
