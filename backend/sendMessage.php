<?php
    session_start();
    require('server.php');

    if (isset($_SESSION['username']) && isset($_POST['group_id']) && isset($_POST['message'])) {
        $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
        $group_id = intval($_POST['group_id']);
        $message = mysqli_real_escape_string($db_connection, $_POST['message']);

        // Fetch user_id
        $query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
        $result = mysqli_query($db_connection, $query);
        $user = mysqli_fetch_assoc($result);
        $sender_id = $user['user_id'];

        // Insert message
        $query = "INSERT INTO Messages (group_id, sender_id, message_content) VALUES ('$group_id', '$sender_id', '$message')";
        $result = mysqli_query($db_connection, $query);

        if ($result) {
            echo "Message sent successfully";
        } else {
            echo "Error sending message: " . mysqli_error($db_connection); // Add error message to the response
        }
    } else {
        echo "Invalid data";
    }
?>
