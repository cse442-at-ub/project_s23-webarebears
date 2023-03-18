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

    $query = "
        SELECT task_id, description
        FROM Tasks
        WHERE assigned_to = '$user_id' AND status = 'pending'
        ORDER BY due_date ASC
    ";

    $result = mysqli_query($db_connection, $query);

    header('Content-Type: application/json');

    if (!$result) {
        echo json_encode(['error' => "Error: " . mysqli_error($db_connection)]);
        exit();
    }

    $tasks = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[] = $row;
    }

    echo json_encode($tasks);
?>
