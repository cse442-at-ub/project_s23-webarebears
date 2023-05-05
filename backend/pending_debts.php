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
        WHERE assigned_to = '$user_id' AND status = 'pending'
        ORDER BY due_date ASC
    ";

    $result = mysqli_query($db_connection, $query);

    $pending_debts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $pending_debts[] = $row;
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pending Debts</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

    <div>
        <div class="debts-container">
            <h2>Pending Debts</h2>
            <div class="pending-debts">
                <?php foreach ($pending_debts as $debt): ?>
                    <div id="debts">
                        <p><?php echo htmlspecialchars($debt['description']); ?></p>
                        <span> - Amount: <?php echo htmlspecialchars($debt['amount']); ?></span><br>
                        <span> - Due on: <?php echo htmlspecialchars($debt['due_date']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</html>
