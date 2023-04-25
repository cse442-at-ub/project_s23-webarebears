<?php
header("Content-Type: application/json");
session_start();

require('server.php');

if (!isset($_GET['group_id'])) {
    echo json_encode(['error' => 'Missing group_id parameter.']);
    exit();
}

$group_id = intval($_GET['group_id']);

$query = "SELECT `User Accounts`.user_id, `User Accounts`.username FROM Group_Members INNER JOIN `User Accounts` ON Group_Members.user_id = `User Accounts`.user_id WHERE Group_Members.group_id = '$group_id'";
$result = mysqli_query($db_connection, $query);

$members = [];
$current_user_id = $_SESSION['user_id']; // Retrieve the current user's ID from the session

while ($row = mysqli_fetch_assoc($result)) {
    if ($row['user_id'] != $current_user_id) { // Check if the user_id in the fetched row is not equal to the current user's ID
        $members[] = [
            'user_id' => $row['user_id'],
            'username' => $row['username'],
        ];
    }
}

echo json_encode($members);
?>
