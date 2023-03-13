<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Login</title>
    <link rel="stylesheet" href="styles/login_style.css"/>
</head>
<body>
<?php
    require('server.php');
    session_start();
    // When form submitted, check and create user session.
    if (isset($_POST['username'])) {
        $username = stripslashes($_REQUEST['username']);    // removes backslashes
        $username = mysqli_real_escape_string($db_connection, $username);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($db_connection, $password);
        // Check user exists in the database
        $query = "SELECT * FROM `User Accounts` WHERE username='$username' AND password='$password'";

        $result = mysqli_query($db_connection, $query) or die(mysqli_error($db_connection));
        $rows = mysqli_num_rows($result);
        if ($rows == 1) {
            $_SESSION['username'] = $username;
            // Redirect to user dashboard page
            header("Location: home.html");
            exit();
        } else {
            echo "<div class='form'>
                    <h3>Incorrect Username/password.</h3><br/>
                    <p class='link'>Click here to <a href='login.php'>Login</a> again.</p>
                    </div>";
        }
    } else {
?>
    <form class="form" method="post" name="login">
        <img id="logo" src="images/temp-logo.png" alt="Logo">
        <h1 class="login-title">Login With Your Username</h1>
        <input type="text" class="login-input" name="username" placeholder="Username" autofocus="true"/>
        <input type="password" class="login-input" name="password" placeholder="Password"/>
        <input type="submit" value="Login" name="submit" class="login-button"/>
        <p class="link"><a href="register.php">New Registration</a></p>
  </form>
<?php
    }
?>
</body>
</html>
