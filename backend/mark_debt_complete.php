<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo "Error: Not logged in";
    exit();
}

require('server.php');
$debt_id = intval($_POST['debt_id']);

$query = "UPDATE `Users_Debts` SET status='completed' WHERE debt_id='$debt_id'";
if (mysqli_query($db_connection, $query)) {
    echo "success";
} else {
    echo "Error: Unable to mark debt as complete.";
}
?>
