<?php
header('Content-Type: application/json');

require('server.php');
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode([]);
    exit();
}

if (!isset($_GET['group_id'])) {
    echo json_encode([]);
    exit();
}

$group_id = mysqli_real_escape_string($db_connection, $_GET['group_id']);
$username = mysqli_real_escape_string($db_connection, $_SESSION['username']);

$query = "SELECT u.user_id, u.username FROM `User Accounts` u
          WHERE u.user_id NOT IN (SELECT gm.user_id FROM `Group_Members` gm WHERE gm.group_id = '$group_id')
          AND u.user_id IN (SELECT f.friend_user_id FROM `friends` f
                            WHERE f.user_id = (SELECT ua.user_id FROM `User Accounts` ua WHERE ua.username = '$username'))";

$result = mysqli_query($db_connection, $query);
$friends = [];

while ($row = mysqli_fetch_assoc($result)) {
    $friends[] = $row;
}

echo json_encode($friends);
?>
