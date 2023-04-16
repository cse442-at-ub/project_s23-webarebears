<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Register</title>
    <link rel="stylesheet" href="styles/register_style.css"/>
</head>
<body>
<?php
require_once "server.php";

// Initialize variables
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Check if the database connection is successful
if (!$db_connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Process form data when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (strlen(trim($_POST["username"])) < 4) {
        $username_err = "Username must have at least 4 characters.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting into database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        
        // Prepare an insert statement
        $sql = "INSERT INTO `User Accounts` (username, password) VALUES (?, ?)";
        
        if ($stmt = mysqli_prepare($db_connection, $sql)) {
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later...." . mysqli_error($db_connection);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($db_connection);
}
?>
    <form class="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <img id="logo" src="images/temp-logo.png" alt="Logo">
        <h2 id="register-title">REGISTER</h2>
        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" placeholder = "Username">
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>" placeholder = "Password">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">

    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>" placeholder = "Confirm Password">
    <span class="help-block"><?php echo $confirm_password_err; ?></span>
    </div>
    <input type="submit" class="register-button" value="Register">
    <p id="login_link">Already have an account? <a href="login.php">Login here</a>.</p>
    <p id="user_concern">Your personal details are safe with us</p>
    <p id="user_concern">Read our Privacy Policy and Terms and Conditions</p>
    </form>