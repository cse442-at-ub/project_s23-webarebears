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
    <link rel="stylesheet" href="styles/balances_style.css"/>
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

    <main>
        <div class="debt_owed_to_you">
            <label for="total_owed">Debt Owed To You </label>
            <label id="total_owed">$40</label>
            <p>From Groups</p>
            <button id="debt_owed_to_you_group" onclick="showDept()">
                Group 1 <br><br><br><br> $30
            </button>
            <div id="debtor" style="display:none;">
                <button> Junstin  &emsp; $30 <br>Click to Remove</button>
            </div>
        </div>

        <div class="debt_you_owe">
            <label for="total_dept">Dept you Owe </label>
            <label id="total_dept">$60</label>
            <p>To Groups</p>
        </div>
        <div class="your_task">
            <h3 style="color: white; font-size: larger; font-weight: 400;">Your Tasks:</h3>
            <p style="color: white;"> Select Tasks that you have finished!</p>
            <div id="tasks">Your Tasks: 
				<p id="tasks-direction"> Select Tasks that you have finished!</p>
			</div>
            <button id="complete-tasks-btn" onclick="completeTasks()">Complete</button>
        </div>
    </main> 

    <script>
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

        function showDept(){
            // get the div element that contains the buttons
        var buttonsDiv = document.getElementById("debtor");

        // set the display style of the div to "block"
        buttonsDiv.style.display = "block";

        var groupDiv = document.getElementById("debt_owed_to_you_group");
        groupDiv.style.display = "none";
        }
                
        fetchMyTasks()
</script>

</body>
</html>