<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    require('server.php');

    $username = $_SESSION['username'];
    $message = '';

    if (isset($_POST['submit'])) {
        $currentUsername = mysqli_real_escape_string($db_connection, $username);
        $newPassword = mysqli_real_escape_string($db_connection, $_POST['new_password']);

        $query = "UPDATE `User Accounts` SET password = '$newPassword' WHERE username = '$currentUsername'";
        $result = mysqli_query($db_connection, $query);

        if ($result) {
            $message = 'Your password has been updated successfully.';
        } else {
            $message = 'An error occurred while updating your password.';
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Settings</title>
    <link rel="stylesheet" href="styles/home_style.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <h1>Settings</h1>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password">
        <br>
        <input type="submit" name="submit" value="Change Password" onclick="return confirm('Are you sure you want to update your password?');">

        <a href="profile.php" class="back-button">Back to Profile</a>

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
