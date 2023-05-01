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
    SELECT fr.request_id, fr.sender_id, ua.username AS sender_username
    FROM Friend_Requests fr
    JOIN `User Accounts` ua ON fr.sender_id = ua.user_id
    WHERE fr.receiver_id = '$user_id' AND fr.status = 'pending'
";

$result = mysqli_query($db_connection, $query);

header('Content-Type: application/json');

if (!$result) {
    echo json_encode(['error' => "Error: " . mysqli_error($db_connection)]);
    exit();
}

$friend_requests = [];

while ($row = mysqli_fetch_assoc($result)) {
    $friend_requests[] = $row;
}

echo json_encode($friend_requests);
?>
