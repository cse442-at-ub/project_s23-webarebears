<?php

    require('server.php');

    $username = $_SESSION['username'];
    $message = '';

    if (isset($_POST['submit1'])) {
        $currentUsername = mysqli_real_escape_string($db_connection, $username);
        $newUsername = mysqli_real_escape_string($db_connection, $_POST['new_username123']);
        $newPassword = mysqli_real_escape_string($db_connection, $_POST['ddddd']);

        $query = "UPDATE `User Accounts` SET username = '$newUsername', password = '$newPassword' WHERE username = '$currentUsername'";
        $result = mysqli_query($db_connection, $query);

        if ($result) {
            $message = 'Your username and/or password have been updated successfully.';
            $_SESSION['username'] = $newUsername;
        } else {
            $message = 'An error occurred while updating your username and/or password.';
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles/profiletab.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <h1>Edit Profile</h1>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <p>Change Username and Password</p>
        <input type="text" name="new_username123" id="new_username123" placeholder="New Username">
        <br>
        <input type="password" name="ddddd" id="ddddd" placeholder="New Password">
        <br>
        <button type="submit1" name="submit1" value="Save Changes" onclick="return confirm('Are you sure you want to update your username and/or password?');">Save Changes</button>

        <!--<a href="profile.php" class="back-button">Back to Profile</a>-->
    <style>
        .back-button {
            display: inline-block;
            padding: 8px 12px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }

        .back-button:hover {
            background-color: #555;
        }
    </style>
    </form>
</body>
</html>
