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
        
        // Prepare a SELECT statement to check user exists in the database
        $query = "SELECT * FROM `User Accounts` WHERE username=?";

        if ($stmt = mysqli_prepare($db_connection, $query)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $username);
            
            // Execute the prepared statement
            mysqli_stmt_execute($stmt);
            
            // Store the result
            mysqli_stmt_store_result($stmt);
            
            // Check if the username exists
            if (mysqli_stmt_num_rows($stmt) == 1) {
                // Bind result variables
                mysqli_stmt_bind_result($stmt, $id, $username, $stored_password);
                
                // Fetch the result
                mysqli_stmt_fetch($stmt);
                
                // Verify the password
                if (password_verify($password, $stored_password)) {
                    // Password is correct, start a new session
                    session_start();
                    
                    // Store data in session variables
                    $_SESSION['username'] = $username;
                    
                    // Redirect to user dashboard page
                    header("Location: home.php");
                    exit();
                } else {
                    // Password is not valid, display a generic error message
                    echo "<div class='form'>
                            <h3>Incorrect Username/password.</h3><br/>
                            <p class='link'>Click here to <a href='login.php'>Login</a> again.</p>
                          </div>";
                }
            } else {
                // No account found with the given username
                echo "<div class='form'>
                        <h3>Incorrect Username/password.</h3><br/>
                        <p class='link'>Click here to <a href='login.php'>Login</a> again.</p>
                      </div>";
            }
        }
        // Close statement
        mysqli_stmt_close($stmt);
        
    } else {
?>
<body>
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