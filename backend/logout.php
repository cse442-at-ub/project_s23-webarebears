<?php
    session_start();
    session_destroy(); // destroy all sessions

    header("Location: login.php"); // redirect to login page
    exit();
?>
