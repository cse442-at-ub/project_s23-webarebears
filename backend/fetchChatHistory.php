<?php
require('server.php');

if (isset($_GET['group_id'])) {
    $group_id = $_GET['group_id'];

    $query_group_name = "SELECT group_name FROM `Groups` WHERE group_id = '$group_id'";
    $result_group_name = mysqli_query($db_connection, $query_group_name);

    if ($result_group_name) {
        $group_data = mysqli_fetch_assoc($result_group_name);
        $group_name = $group_data['group_name'];
    } else {
        $group_name = "Unknown";
    }


    $query = "SELECT m.message_id, m.sender_id, m.message_content, m.timestamp, u.username FROM Messages AS m INNER JOIN `User Accounts` AS u ON m.sender_id = u.user_id WHERE m.group_id = '$group_id' ORDER BY m.timestamp ASC";
    $result = mysqli_query($db_connection, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $message_id = $row['message_id'];
            $sender_id = $row['sender_id'];
            $username = $row['username'];
            $message_content = $row['message_content'];
            $timestamp = $row['timestamp'];
            echo "<p><strong>$username ($timestamp):</strong> $message_content</p>";
        }
    } else {
        echo "Query execution failed\n";
    }
} else {
    echo "Group ID not set\n";
}
?>
