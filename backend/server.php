<?php
global $db_connection;

$serverhost = "cse442_2023_spring_team_ac_db";
$username = "cjtoy";
$password = "50341034";
$servername = "oceanus.cse.buffalo.edu:3306";

// Create connection
$db_connection = new mysqli($servername, $username, $password, $serverhost);

// Check connection
if ($db_connection->connect_error) {
    die("Connection failed: " . $db_connection->connect_error);
} else {
    echo "Connected successfully";
}
?>