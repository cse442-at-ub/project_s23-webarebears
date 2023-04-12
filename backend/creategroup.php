<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Group</title>
    <link rel="stylesheet" href="styles/creategroup_style.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<header>
		<nav class="nav-left">
			<a href="profile.php">
				<img id="profile-pic" src="images/profile-temp.png" alt="Profile Icon">
			</a>
			<a href="home.php" id="home" >Home</a>
			<a id="tasksAndBalances" href="tasksAndBalances.php">Tasks and Balances</a>
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
    <div class="container">
        <h2>Create a Group</h2>
        <form method="post" onsubmit="return validateForm()">
            <label for="groupname">Group Name:</label>
            <input type="text" id="groupname" name="groupname">
            <p>Members:</p>

            <select multiple size="6" style="width: 300px;" name="selected_friends[]" multiple>
                <?php
                require('server.php');
                session_start();

                if (!isset($_SESSION['username'])) {
                    header("Location: login.php");
                    exit();
                }

                $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
                $query = "SELECT friend_username FROM `Friends` WHERE username='$username'";
                $result = mysqli_query($db_connection, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $friend_username = $row['friend_username'];
                    echo "<option value='" . $friend_username . "'>" . $friend_username . "</option>";
                }
                ?>
            </select>
            <div>Hold down the Ctrl (windows) or Command (Mac) button to select multiple members.</div>
            <button id="create-btn" type="submit" name="create_group" value="Create">Create</button>
            <button id="cancel-btn" type="button" value="Cancel" onclick="cancel()">Cancel</button>
        </form>
    </div>
</main>

<footer>
    <p></p>
</footer>

<script>
    function cancel() {
        alert("Group creation has been canceled.");
        window.location.href = "messages.php";
    }

	function validateForm() {
        const groupName = document.getElementById("groupname").value;
        const friendsList = document.querySelector("select[name='selected_friends[]']");

        if (groupName.trim() === '') {
            alert("Group name cannot be empty.");
            return false;
        }

        if (friendsList.selectedOptions.length === 0) {
            alert("You must select at least one friend.");
            return false;
        }

        return true;
    }
</script>

<?php
if (isset($_POST['create_group']) && !empty($_POST['groupname']) && !empty($_POST['selected_friends'])) {
    $group_name = mysqli_real_escape_string($db_connection, $_POST['groupname']);
    $query = "INSERT INTO `Groups` (group_name) VALUES ('$group_name')";
    mysqli_query($db_connection, $query);
    $group_id = mysqli_insert_id($db_connection);

    $selected_friends = $_POST['selected_friends'];
    array_push($selected_friends, $username);

    foreach ($selected_friends as $friend) {
        $friend = mysqli_real_escape_string($db_connection, $friend);
        $query = "INSERT INTO `Group_Members` (group_id, user_id) SELECT $group_id, user_id FROM `User Accounts` WHERE username='$friend'";
        mysqli_query($db_connection, $query);
    }

    echo "<script>alert(\"Group '" . $group_name . "' has been created.\"); window.location.href = 'messages.php';</script>";
}


?>

</body>
</html>
