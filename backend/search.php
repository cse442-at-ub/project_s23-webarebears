<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        echo json_encode(['error' => 'Not authenticated']);
        exit();
    }

    require('server.php');

    $searchQuery = $_GET['q'] ?? '';

    if (trim($searchQuery) === '') {
        echo json_encode(['error' => 'Empty search query']);
        exit();
    }

    $searchQuery = mysqli_real_escape_string($db_connection, $searchQuery);
    $current_user = mysqli_real_escape_string($db_connection, $_SESSION['username']);

    $query = "
    SELECT username as result, 'User Accounts' as type
    FROM `User Accounts`
    WHERE username LIKE '%$searchQuery%'
    AND user_id NOT IN (
        SELECT friend_user_id
        FROM `friends`
        WHERE user_id = (
            SELECT user_id
            FROM `User Accounts`
            WHERE username = '$current_user'
        )
    )
";


    $result = mysqli_query($db_connection, $query);

    if ($result) {
        $results = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
        echo json_encode(['results' => $results]);
    } else {
        echo json_encode(['error' => 'Error executing search query']);
    }
?>
