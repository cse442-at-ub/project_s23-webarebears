<?php
require('server.php');
session_start();

if (!isset($_SESSION['username'])) {
    echo "Not logged in";
    exit();
}

if (!isset($_POST['group_id']) || !isset($_POST['friends'])) {
    echo "Invalid input";
    exit();
}

$group_id = mysqli_real_escape_string($db_connection, $_POST['group_id']);
$friends = json_decode($_POST['friends'], true);

foreach ($friends as $friend_user_id) {
    $friend_user_id = mysqli_real_escape_string($db_connection, $friend_user_id);
    $query = "INSERT INTO `Group_Members` (group_id, user_id) VALUES ('$group_id', '$friend_user_id')";
    mysqli_query($db_connection, $query);
}

echo "Friends added to the group";
?>
