<?php
$serverhost = "cse442_2023_spring_team_ac_db";
$username = "cjtoy";
$password = "50341034";
$servername = "oceanus.cse.buffalo.edu";

// Create connection
$conn = new mysqli($servername, $username, $password, $serverhost);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
?>