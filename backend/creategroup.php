<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Group</title>
</head>
<body>
<header>
    <nav>
        <a href="home.php">Home</a>
        <a href="tasksAndBalances.php">Tasks and Balances</a>
        <a href="messages.php">Messages</a>
    </nav>
</header>

<main>
    <h1>Add friends to group</h1>
    <form method="post" onsubmit="return validateForm()">
        <label for="groupname">Group Name:</label>
        <input type="text" id="groupname" name="groupname">
        <p>Friends List:</p>
        <select multiple size="10" style="width: 300px;" name="selected_friends[]" multiple>
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

        <input type="submit" name="create_group" value="Create"/>
        <input type="button" value="Cancel" onclick="cancel()"/>
    </form>
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
