<?php
session_start();
require('server.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    $group_id = $_POST['group_id'];
    $assigned_to = $_POST['friend'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    // Get the creator_id using the username
    $creator_username = $_SESSION['username'];
    $creator_query = "SELECT user_id FROM `User Accounts` WHERE username = ?";
    $stmt_creator = mysqli_prepare($db_connection, $creator_query);
    mysqli_stmt_bind_param($stmt_creator, "s", $creator_username);
    mysqli_stmt_execute($stmt_creator);
    $creator_result = mysqli_stmt_get_result($stmt_creator);
    $creator_id = mysqli_fetch_assoc($creator_result)['user_id'];


    $assigned_to_query = "SELECT username FROM `User Accounts` WHERE user_id = ?";
    $stmt_assigned_to = mysqli_prepare($db_connection, $assigned_to_query);
    mysqli_stmt_bind_param($stmt_assigned_to, "i", $assigned_to);
    mysqli_stmt_execute($stmt_assigned_to);
    $assigned_to_result = mysqli_stmt_get_result($stmt_assigned_to);
    $assigned_to_username = mysqli_fetch_assoc($assigned_to_result)['username'];

    $query = "INSERT INTO Tasks (group_id, assigned_to, description, due_date) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($db_connection, $query);
    mysqli_stmt_bind_param($stmt, "iiss", $group_id, $assigned_to, $description, $due_date);

    if (mysqli_stmt_execute($stmt)) {
        $task_id = mysqli_insert_id($db_connection);
        $message = "User {$creator_username} assigned to user {$assigned_to_username}: '{$description}' with due date {$due_date}.";
        $querys = "INSERT INTO Messages (group_id, sender_id, message_content) VALUES (?, ?, ?)";
        $stmt2 = mysqli_prepare($db_connection, $querys);
        mysqli_stmt_bind_param($stmt2, "iis", $group_id, $creator_id, $message);

        if (mysqli_stmt_execute($stmt2)) {
            echo "Message sent successfully";
        } else {
            echo "Error sending message: " . mysqli_error($db_connection);
        }

        echo "Task created successfully.";
    } else {
        echo "Error creating task: " . mysqli_error($db_connection);
    }
}

?>
