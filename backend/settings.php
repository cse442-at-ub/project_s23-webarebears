<?php
    require('server.php');

    $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
    $query1 = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $result = mysqli_query($db_connection, $query1);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];

    $message = '';

    if (isset($_POST['submit'])) {
        $currentUsername = mysqli_real_escape_string($db_connection, $username);
        $newPassword = mysqli_real_escape_string($db_connection, $_POST['new_password']);
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE `User Accounts` SET password='$hashedNewPassword' WHERE user_id='$user_id'";
        mysqli_query($db_connection, $query);

        if ($result2) {
            $message = 'Your password has been updated successfully. ' . $currentUsername;
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
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <h1>Settings</h1>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    
    <p>Change Password</p>
    <form method="post" action="">
        
        <input type="password" name="new_password" id="new_password" placeholder="New Password"></input>
        <br>
        <button type="submit" name="submit" value="Change Password" onclick="return confirm('Are you sure you want to update your password?');">Change Password</button>

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
