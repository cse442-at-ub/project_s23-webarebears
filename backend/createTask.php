<?php
session_start();
require('server.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    $group_id = mysqli_real_escape_string($db_connection, $_POST['group_id']);
    $assigned_to = mysqli_real_escape_string($db_connection, $_POST['friend']);
    $description = mysqli_real_escape_string($db_connection, $_POST['description']);
    $due_date = mysqli_real_escape_string($db_connection, $_POST['due_date']);

    $query = "INSERT INTO Tasks (group_id, assigned_to, description, due_date) VALUES ('$group_id', '$assigned_to', '$description', '$due_date')";

    if (mysqli_query($db_connection, $query)) {
        echo "Task created successfully.";
    } else {
        echo "Error creating task: " . mysqli_error($db_connection);
    }
}
?>
