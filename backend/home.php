<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>My Website</title>
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

	<footer>
		<!--profile pic break nav bar-->
		<a id="profile" href="">
			<img id="profile-pic" src="images/profile-temp.png">
		</a>
		<p></p>
	</footer>
</body>
</html>