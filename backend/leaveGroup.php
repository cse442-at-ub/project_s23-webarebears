<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    require('server.php');

    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    $group_id = $_POST['group_id'];

    $query = "DELETE FROM `Group_Members` WHERE group_id = '$group_id' AND user_id = '$user_id'";
    $result = mysqli_query($db_connection, $query);

    if ($result) {
        // Announce that the user has left the group
        $message = "User {$username} has left the group.";
        $querys = "INSERT INTO Messages (group_id, sender_id, message_content) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($db_connection, $querys);
        mysqli_stmt_bind_param($stmt, "iis", $group_id, $user_id, $message);

        if (mysqli_stmt_execute($stmt)) {
            echo "Message sent successfully";
        } else {
            echo "Error sending message: " . mysqli_error($db_connection);
        }

        echo "You have successfully left the group.";
    } else {
        echo "Error leaving group: " . mysqli_error($db_connection);
    }
?>
