<?php


    require('server.php');

    $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
    $query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];

    $query = "
        SELECT debt_id, description, amount, due_date
        FROM Users_Debts
        WHERE assigned_to = '$user_id' AND status = 'completed'
        ORDER BY due_date ASC
    ";

    $result = mysqli_query($db_connection, $query);

    $completed_debts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $completed_debts[] = $row;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Completed Debts</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

    <div class="completed-debts-container">
        <h2>Completed Debts</h2>
        <div class="completed-debts">
            <?php foreach ($completed_debts as $debt): ?>
                <div id="debts">
                    <h4><?php echo htmlspecialchars($debt['description']); ?></h4>
                    <span> - Amount: <?php echo htmlspecialchars($debt['amount']); ?></span>
                    <span> - Completed on: <?php echo htmlspecialchars($debt['due_date']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</html>
