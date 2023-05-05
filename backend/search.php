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

    $query = "
        (SELECT description as result, 'Users_debts' as type FROM `Users_debts` WHERE description LIKE '%$searchQuery%')
        UNION
        (SELECT description as result, 'Tasks' as type FROM `Tasks` WHERE description LIKE '%$searchQuery%')
        UNION
        (SELECT username as result, 'User Accounts' as type FROM `User Accounts` WHERE username LIKE '%$searchQuery%')
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
