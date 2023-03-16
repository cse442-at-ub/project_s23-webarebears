<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Messages</title>
    <style>
        /* CSS styles go here */
        header {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
        nav {
            display: flex;
        }
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
        }
        nav a:hover {
            background-color: #555;
        }
        footer {
            background-color: #333;
            color: #fff;
            padding: 10px;
            text-align: center;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="home.html">Home</a>
            <a href="tasksAndBalances.html">Tasks and Balances</a>
            <a href="messages.php">Messages</a>
            <form method="post" action="">
                <input type="submit" name="logout" value="Logout">
            </form>
        </nav>
    </header>

    <main>
        <!-- Main content goes here -->
        <p> Messages Page </p>
        <button onclick="window.location.href='addfriends.php'">Add Friends</button>
        <button onclick="window.location.href='creategroup.html'">Create Group</button>
    </main>

    <footer>
        <p></p>
    </footer>
</body>
</html>
