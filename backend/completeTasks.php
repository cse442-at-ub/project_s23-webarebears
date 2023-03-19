<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header("Location: home.php");
        exit();
    }

    require('server.php');

    $postData = json_decode(file_get_contents('php://input'), true);
    $taskIds = $postData['taskIds'];

    if (!is_array($taskIds) || count($taskIds) == 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid task IDs']);
        exit();
    }

    $escapedTaskIds = [];
    foreach ($taskIds as $taskId) {
        $escapedTaskIds[] = mysqli_real_escape_string($db_connection, $taskId);
    }

    $taskIdsString = implode(', ', $escapedTaskIds);

    $query = "UPDATE Tasks SET status = 'completed' WHERE task_id IN ($taskIdsString)";
    $result = mysqli_query($db_connection, $query);

    header('Content-Type: application/json');

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => "Error: " . mysqli_error($db_connection)]);
    }
?>
