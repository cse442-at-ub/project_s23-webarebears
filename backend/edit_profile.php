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
        $newUsername = mysqli_real_escape_string($db_connection, $_POST['new_username']);
        $newPassword = mysqli_real_escape_string($db_connection, $_POST['new_password']);

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
    <link rel="stylesheet" href="styles/home_style.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <h1>Edit Profile</h1>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="new_username">New Username:</label>
        <input type="text" name="new_username" id="new_username" value="<?php echo htmlspecialchars($username); ?>">
        <br>
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password">
        <br>
        <input type="submit" name="submit" value="Save Changes" onclick="return confirm('Are you sure you want to update your username and/or password?');">

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
