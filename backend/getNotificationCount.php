<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    require('server.php');

    $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
    $query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];

    // Get count of pending tasks
    $query = "
        SELECT COUNT(task_id) as pending_tasks_count
        FROM Tasks
        WHERE assigned_to = '$user_id' AND status = 'pending'
    ";
    $result = mysqli_query($db_connection, $query);
    $row = mysqli_fetch_assoc($result);
    $pending_tasks_count = $row['pending_tasks_count'];

    // Get count of pending friend requests
    $query = "
        SELECT COUNT(request_id) as pending_requests_count
        FROM Friend_Requests
        WHERE receiver_id = '$user_id' AND status = 'pending'
    ";
    $result = mysqli_query($db_connection, $query);
    $row = mysqli_fetch_assoc($result);
    $pending_requests_count = $row['pending_requests_count'];

    // Get count of pending debts
    $query = "
        SELECT COUNT(debt_id) as pending_debts_count
        FROM Users_Debts
        WHERE assigned_to = '$user_id' AND status = 'pending'
    ";
    $result = mysqli_query($db_connection, $query);
    $row = mysqli_fetch_assoc($result);
    $pending_debts_count = $row['pending_debts_count'];

    // Add up all the counts
    $total_count = $pending_tasks_count + $pending_requests_count + $pending_debts_count;

    header('Content-Type: application/json');
    echo json_encode(['newCount' => $total_count]);
?>