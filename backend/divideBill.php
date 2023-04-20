<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['username'])) {
    echo "Error: Not logged in";
    exit();
}

require('server.php');
$username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
$query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
$result = mysqli_query($db_connection, $query);
$user = mysqli_fetch_assoc($result);
$sender_id = $user['user_id'];

$group_id = intval($_POST['group_id']);
$friends = json_decode($_POST['friends']);
$description = mysqli_real_escape_string($db_connection, $_POST['description']);
$amount = floatval($_POST['amount']);
$due_date = mysqli_real_escape_string($db_connection, $_POST['due_date']);

$amount_per_friend = $amount / count($friends);

foreach ($friends as $friend_id) {
    $friend_id = intval($friend_id);
    $query = "INSERT INTO `Users_Debts` (group_id, assigner, assigned_to, description, amount, due_date, status) VALUES ('$group_id', '$sender_id', '$friend_id', '$description', '$amount_per_friend', '$due_date', 'pending')";
    if (!mysqli_query($db_connection, $query)) {
        echo "Error: Unable to insert data into User_Debts table. Error details: " . mysqli_error($db_connection);
        exit();
    }
}

echo "Success: Bill divided and inserted into User_Debts table.";

?>
