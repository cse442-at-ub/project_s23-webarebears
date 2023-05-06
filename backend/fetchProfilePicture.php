<?php
session_start();
require('server.php');

$username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
$query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
$result = mysqli_query($db_connection, $query);
$user = mysqli_fetch_assoc($result);
$user_id = $user['user_id'];

$query = "SELECT image_path FROM `Profile_Pictures` WHERE user_id='$user_id'";
$result = mysqli_query($db_connection, $query);
$row = mysqli_fetch_assoc($result);

if ($row) {
    $image_path = $row['image_path'];
    $ext = pathinfo($image_path, PATHINFO_EXTENSION);
    
    switch (strtolower($ext)) {
        case 'jpg':
        case 'jpeg':
            header("Content-type: image/jpeg");
            break;
        case 'png':
            header("Content-type: image/png");
            break;
        default:
            header("Content-type: image/jpeg");
    }
    
    echo file_get_contents($image_path);
} else {
    header("Location: images/initial-logo.png");
}
?>
