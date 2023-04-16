<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    require('server.php');

    $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
    $query = "SELECT user_id FROM `User_Accounts` WHERE username='$username'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];

    $query = "
        SELECT debt_id, group_id, description, amount, due_date, status
        FROM Users_Debts
        WHERE assigned_to = '$user_id' AND status = 'pending'
        ORDER BY due_date ASC
    ";

    $result = mysqli_query($db_connection, $query);

    header('Content-Type: application/json');

    if (!$result) {
        echo json_encode(['error' => "Error: " . mysqli_error($db_connection)]);
        exit();
    }

    $debts = [];
    $total_debt = 0;

    while ($row = mysqli_fetch_assoc($result)) {
        $debts[] = $row;
        $total_debt += $row['amount'];
    }

    $response = [
        'debts' => $debts,
        'total_debt' => $total_debt
    ];

    echo json_encode($response);
?>
