<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    require('server.php');

    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
    $query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];

    // Fetch groups where the user has assigned debts
    $query = "SELECT DISTINCT group_id FROM `Users_Debts` WHERE assigner='$user_id' AND status='pending'";
    $assigned_groups_result = mysqli_query($db_connection, $query);

    // Calculate total amount owed to the user
    $query_total_owed = "SELECT SUM(amount) as total_owed FROM `Users_Debts` WHERE assigner='$user_id' AND status='pending'";
    $result_total_owed = mysqli_query($db_connection, $query_total_owed);
    $total_owed = mysqli_fetch_assoc($result_total_owed)['total_owed'];
    $total_owed = $total_owed ? $total_owed : 0;

    // Fetch groups where the user owes debts
    $query_owed_groups = "SELECT DISTINCT group_id FROM `Users_Debts` WHERE assigned_to='$user_id' AND status='pending'";
    $owed_groups_result = mysqli_query($db_connection, $query_owed_groups);

    // Calculate total amount the user owes
    $query_total_dept = "SELECT SUM(amount) as total_dept FROM `Users_Debts` WHERE assigned_to='$user_id' AND status='pending'";
    $result_total_dept = mysqli_query($db_connection, $query_total_dept);
    $total_dept = mysqli_fetch_assoc($result_total_dept)['total_dept'];
    $total_dept = $total_dept ? $total_dept : 0;

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Home</title>
    <link rel="stylesheet" href="styles/balances_style.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
</head>

<body id="homepage">

<!-- Add JavaScript to handle clicking on a group and marking a debt as complete -->
<script>
function hideDebtsOwedToYou() {
    let debtContainers = document.querySelectorAll('.debts-container1');
    console.log(debtContainers); // Debugging line

    debtContainers.forEach(function(container) {
        container.style.display = 'none';
    });

    let groupNames = document.querySelectorAll('.group-name1');
    console.log(groupNames); // Debugging line

    groupNames.forEach(function(name) {
        name.style.display = 'block';
    });

    let backButton = document.getElementById('back-btn1');
    backButton.style.display = 'none';
    //backButton.removeEventListener('click', hideDebtsOwedToYou);

    document.getElementById('from_groups').innerHTML = "From Groups";
}

function hideDebtYouOwe() {
    let debtContainers = document.querySelectorAll('.debts-container2');
    console.log(debtContainers); // Debugging line

    debtContainers.forEach(function(container) {
        container.style.display = 'none';
    });

    let groupNames = document.querySelectorAll('.group-name2');
    console.log(groupNames); // Debugging line

    groupNames.forEach(function(name) {
        name.style.display = 'block';
    });

    let backButton = document.getElementById('back-btn2');
    backButton.style.display = 'none';
    //backButton.removeEventListener('click', hideDebtsYouOwe);

    document.getElementById('to_groups').innerHTML = "To Groups";
}

function showDebtsOwedToYou(groupId, group_name) {
    let debtsContainer = document.getElementById('debts-owed-container-' + groupId);
        debtsContainer.style.display = debtsContainer.style.display === 'none' ? 'block' : 'none';
        
        let groupNames = document.querySelectorAll('.group-name1');
    groupNames.forEach(function(name) {
        if (name.id !== 'group-name-' + groupId) {
            name.style.display = 'none';
        } else {
            name.style.display = 'none';
        }
    });

        let backButton = document.getElementById('back-btn1');
        backButton.style.display = 'block';
    }

    function showDebtsYouOwe(groupId) {
        let debtsContainer = document.getElementById('debts-you-owe-container-' + groupId);
        debtsContainer.style.display = debtsContainer.style.display === 'none' ? 'block' : 'none';
        
        let groupNames = document.querySelectorAll('.group-name2');
    groupNames.forEach(function(name) {
        if (name.id !== 'group-name-' + groupId) {
            name.style.display = 'none';
        } else {
            name.style.display = 'none';
        }
    });

        let backButton = document.getElementById('back-btn2');
        backButton.style.display = 'block';
    }

    

    async function markDebtAsComplete(debtId, amount) {
        const response = await fetch('mark_debt_complete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'debt_id=' + debtId
        });

        const result = await response.text();
        if (result === 'success') {
            document.getElementById('debt-' + debtId).style.display = 'none';

            // Update the total amount owed to the user
            let totalOwedLabel = document.getElementById('total_owed');
            let currentTotalOwed = parseFloat(totalOwedLabel.textContent.substring(1));
            let updatedTotalOwed = currentTotalOwed - amount;
            totalOwedLabel.textContent = '$' + updatedTotalOwed.toFixed(2);
        } else {
            alert('Failed to mark debt as complete: ' + result);
        }
    }

    async function markDebtAsCompleteYouOwe(debtId, amount) {
        const response = await fetch('mark_debt_complete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'debt_id=' + debtId
        });

        const result = await response.text();
        if (result === 'success') {
            document.getElementById('debt_you_owe-' + debtId).style.display = 'none';

            // Update the total amount the user owes
            let totalDeptLabel = document.getElementById('total_dept');
            let currentTotalDept = parseFloat(totalDeptLabel.textContent.substring(1));
            let updatedTotalDept = currentTotalDept - amount;
            totalDeptLabel.textContent = '$' + updatedTotalDept.toFixed(2);
        } else {
            alert('Failed to mark debt as complete: ' + result);
        }
    }

</script>


    <header>
        <nav class="nav-left">
            <a href="profile.php">
                <img id="profile-pic" src="images/profile-temp.png" alt="Profile Icon">
            </a>
            <a href="home.php" id="home" >Home</a>
            <a id="tasksAndBalances" href="balances.php">Balances</a>
            <a id="messages" href="messages.php">Messages</a>           
        </nav>
        <nav class="nav-right">
            <input id="search-bar" type="search" placeholder="Search">
            <button type="button" class="icon-button">
                <span class="material-icons">notifications</span>
                <span class="icon-button__badge">2</span>
            </button>
            <form method="post" action="" id='log-out-button'>
                <input type="submit" name="logout" value="Logout">
            </form>
        </nav>
    </header>

    <main>
        <div class="debt_owed_to_you">
                <label for="total_owed">Debt Owed To You </label>
                <label id="total_owed">$<?= htmlspecialchars($total_owed) ?></label>
                <p id="from_groups">From Groups</p>
                <?php while ($row = mysqli_fetch_assoc($assigned_groups_result)) {
                    $group_id = $row['group_id'];
                    $query = "SELECT group_name FROM `Groups` WHERE group_id='$group_id'";
                    $group_result = mysqli_query($db_connection, $query);
                    $group = mysqli_fetch_assoc($group_result);
                    $group_name = $group['group_name'];

                    $query = "SELECT * FROM `Users_Debts` WHERE assigner='$user_id' AND group_id='$group_id' AND status='pending'";
                    $debts_result = mysqli_query($db_connection, $query);
                    if (mysqli_num_rows($debts_result) > 0) {
                ?>
                    <div class="group">
                        <p1 id="group-name-<?= $group_id ?>" class="group-name1" onclick="showDebtsOwedToYou(<?= $group_id ?>, '<?= htmlspecialchars($group_name) ?>')"><?= htmlspecialchars($group_name) ?></p1>
                        <div id="debts-owed-container-<?= $group_id ?>" class="debts-container1" style="display:none;">
                            <?php while ($debt = mysqli_fetch_assoc($debts_result)) {
                                $debt_id = $debt['debt_id'];
                                $assigned_to = $debt['assigned_to'];
                                $description = $debt['description'];
                                $amount = $debt['amount'];

                                $query = "SELECT username FROM `User Accounts` WHERE user_id='$assigned_to'";
                                $assigned_user_result = mysqli_query($db_connection, $query);
                                $assigned_user = mysqli_fetch_assoc($assigned_user_result);
                                $assigned_username = $assigned_user['username'];
                            ?>
                                <div id="debt-<?= $debt_id ?>" class="debt">
                                <p1 onclick="markDebtAsComplete(<?= $debt_id ?>, <?= $amount ?>)"><?= htmlspecialchars($assigned_username) ?> owes you for <?= htmlspecialchars($description) ?>
                                    <span style="float: right;" id="amount">$<?= htmlspecialchars($amount) ?></span>
                                    <br><p2>Click to remove</p2>
                                </p1>
                                </div>
                            <?php } }?>
                        </div>
                    </div>
                <?php } ?>
                <p id="back-btn1" onClick="hideDebtsOwedToYou()">&#9001</p>
            </div>

            <div class="debt_you_owe">
                <label for="total_dept">Debt You Owe </label>
                <label id="total_dept">$<?= htmlspecialchars($total_dept) ?></label>
                <p>To Groups</p>
                <?php while ($row = mysqli_fetch_assoc($owed_groups_result)) {
                    $group_id = $row['group_id'];
                    $query = "SELECT group_name FROM `Groups` WHERE group_id='$group_id'";
                    $group_result = mysqli_query($db_connection, $query);
                    $group = mysqli_fetch_assoc($group_result);
                    $group_name = $group['group_name'];

                    $query = "SELECT * FROM `Users_Debts` WHERE assigned_to='$user_id' AND group_id='$group_id' AND status='pending'";
                    $debts_result = mysqli_query($db_connection, $query);
                    if (mysqli_num_rows($debts_result) > 0) {
                ?>
                    <div class="group">
                        <p1 id="group-name" class="group-name2" onclick="showDebtsYouOwe(<?= $group_id ?>)"><?= htmlspecialchars($group_name) ?></p1>
                        <div id="debts-you-owe-container-<?= $group_id ?>" class="debts-container2" style="display:none;">
                            <?php while ($debt = mysqli_fetch_assoc($debts_result)) {
                                $debt_id = $debt['debt_id'];
                                $assigner = $debt['assigner'];
                                $description = $debt['description'];
                                $amount = $debt['amount'];

                                $query = "SELECT username FROM `User Accounts` WHERE user_id='$assigner'";
                                $assigner_user_result = mysqli_query($db_connection, $query);
                                $assigner_user = mysqli_fetch_assoc($assigner_user_result);
                                $assigner_username = $assigner_user['username'];
                                ?>
                            <div id="debt_you_owe-<?= $debt_id ?>" class="debt">
                            <p1 onclick="markDebtAsComplete(<?= $debt_id ?>, <?= $amount ?>)"> You owe <?= htmlspecialchars($assigned_username) ?> for <?= htmlspecialchars($description) ?>
                                <span style="float: right;" id="amount">$<?= htmlspecialchars($amount) ?></span>
                                <br><p2>Click to remove</p2>
                            </p1>
                        </div>
                    <?php } } ?>
                </div>
            </div>
            <?php } ?>
            <p id="back-btn2" onClick="hideDebtYouOwe()">&#9001</p>
        </div>

<script>
    const groupButtons = document.querySelectorAll('.group-name');

    groupButtons.forEach(function(groupButton) {
        groupButton.addEventListener('click', function() {
            groupButton.classList.toggle('clicked');
        });
    });


</script>


</body>
</html>
