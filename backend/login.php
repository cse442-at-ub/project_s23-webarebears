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
        $username = mysqli_real_escape_string($con, $username);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        // Check user is exist in the database
        $query    = "SELECT * FROM 'users' WHERE username='$username'
                    AND password='" . md5($password) . "'";
        $result = mysqli_query($con, $query) or die(mysql_error());
        $rows = mysqli_num_rows($result);
        if ($rows == 1) {
            $_SESSION['username'] = $username;
            // Redirect to user dashboard page
            header("Location: home.php");
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
        <h1 class="login-title">LOGIN WITH YOUR</h1>
        <h1 class="login-title">USERNAME</h1>
        <input type="text" class="login-input" name="username" placeholder="Username" autofocus="true"/>
        <input type="password" class="login-input" name="password" placeholder="Password"/>
        <input id="login_button" type="submit" value="Login" name="submit" class="login-button"/>
        <p id="registration_link" class="link">New User? <a href="register.php">Click here to Register</a></p>
        <p id="user_concern">Your personal details are safe with us</p>
        <p id="user_concern">Read our Privacy Policy and Terms and Conditions</p>
  </form>
<?php
    }
?>
</body>
</html>