<?php
header("Content-Type: application/json");

require('server.php');

if (!isset($_GET['group_id'])) {
    echo json_encode(['error' => 'Missing group_id parameter.']);
    exit();
}

$group_id = intval($_GET['group_id']);

$query = "SELECT `User Accounts`.user_id, `User Accounts`.username FROM Group_Members INNER JOIN `User Accounts` ON Group_Members.user_id = `User Accounts`.user_id WHERE Group_Members.group_id = '$group_id'";
$result = mysqli_query($db_connection, $query);

$members = [];
while ($row = mysqli_fetch_assoc($result)) {
    $members[] = [
        'user_id' => $row['user_id'],
        'username' => $row['username'],
    ];
}

echo json_encode($members);
?>
