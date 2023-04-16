<?php
session_start();
require('server.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    $group_id = $_POST['group_id'];
    $assigned_to = $_POST['friend'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $status = "Pending"; // Assuming the initial status is always "Pending"

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

    $description = "User {$creator_username} assigned a debt to user {$assigned_to_username}: '{$description}' with amount {$amount} and due date {$due_date}.";

    $query = "INSERT INTO User_Debts (group_id, assigned_to, description, amount, due_date, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($db_connection, $query);
    mysqli_stmt_bind_param($stmt, "iisdss", $group_id, $assigned_to, $description, $amount, $due_date, $status);

    if (mysqli_stmt_execute($stmt)) {
        echo "Debt created successfully.";
    } else {
        echo "Error creating debt: " . mysqli_error($db_connection);
    }
}

?>