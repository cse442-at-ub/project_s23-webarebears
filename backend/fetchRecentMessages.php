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
        SELECT M.group_id, G.group_name, M.message_content, U.username as sender
        FROM Messages M
        JOIN `Groups` G ON M.group_id = G.group_id
        JOIN `User Accounts` U ON M.sender_id = U.user_id
        JOIN `Group_Members` GM ON GM.group_id = G.group_id
        WHERE GM.user_id = '$user_id' AND M.message_id IN (
            SELECT MAX(message_id) FROM Messages GROUP BY group_id
        )
        ORDER BY M.timestamp DESC
        LIMIT 3
    ";

    $result = mysqli_query($db_connection, $query);

    header('Content-Type: application/json');
    
    if (!$result) {
        echo json_encode(['error' => "Error: " . mysqli_error($db_connection)]);
        exit();
    }

    $recent_messages = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $recent_messages[] = $row;
    }

    echo json_encode($recent_messages);
?>
