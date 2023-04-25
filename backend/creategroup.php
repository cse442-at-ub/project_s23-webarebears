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
                $query = "SELECT friend_user_id, username FROM `friends` INNER JOIN `User Accounts` ON friends.friend_user_id = `User Accounts`.user_id WHERE friends.user_id = (SELECT user_id FROM `User Accounts` WHERE username='$username')";
                $result = mysqli_query($db_connection, $query);

                while ($row = mysqli_fetch_assoc($result)) {
                    $friend_user_id = $row['friend_user_id'];
                    $friend_username = $row['username'];
                    echo "<option value='" . $friend_user_id . "'>" . $friend_username . "</option>";
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
    // (script content remains the same)
</script>

<?php
if (isset($_POST['create_group']) && !empty($_POST['groupname']) && !empty($_POST['selected_friends'])) {
    $group_name = mysqli_real_escape_string($db_connection, $_POST['groupname']);
    $query = "INSERT INTO `Groups` (group_name) VALUES ('$group_name')";
    mysqli_query($db_connection, $query);
    $group_id = mysqli_insert_id($db_connection);

    $selected_friends = $_POST['selected_friends'];
    $current_user_query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $current_user_result = mysqli_query($db_connection, $current_user_query);
    $current_user_id = mysqli_fetch_assoc($current_user_result)['user_id'];
    array_push($selected_friends, $current_user_id);

    foreach ($selected_friends as $friend_user_id) {
        $friend_user_id = mysqli_real_escape_string($db_connection, $friend_user_id);
        $query = "INSERT INTO `Group_Members` (group_id, user_id) VALUES ('$group_id', '$friend_user_id')";
        mysqli_query($db_connection, $query);
    }

    echo "<script>alert(\"Group '" . $group_name . "' has been created.\"); window.location.href = 'messages.php';</script>";
}
?>

</body>
</html>