<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    require('server.php');
    $username = mysqli_real_escape_string($db_connection, $_SESSION['username']);
    $query = "SELECT user_id FROM `User Accounts` WHERE username='$username'";
    $result = mysqli_query($db_connection, $query);
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['user_id'];

    $query = "SELECT Groups.group_id, Groups.group_name FROM `Group_Members` INNER JOIN `Groups` ON Group_Members.group_id = Groups.group_id WHERE Group_Members.user_id='$user_id'";
    $result = mysqli_query($db_connection, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Messages</title>
    <style>
        .chat-box {
            position: fixed;
            right: 20px;
            top: 20px;
            width: 300px;
            height: 400px;
            border: 1px solid black;
            display: none;
        }

        .chat-history {
            height: 300px;
            overflow-y: scroll;
        }

        .message-form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="home.html">Home</a>
            <a href="tasksAndBalances.php">Tasks and Balances</a>
            <a href="messages.php">Messages</a>
            <form method="post" action="">
                <input type="submit" name="logout" value="Logout">
            </form>
        </nav>
    </header>

    <main>
        <!-- Main content goes here -->
        <p> Messages Page </p>
        <button onclick="window.location.href='addfriends.php'">Add Friends</button>
        <button onclick="window.location.href='creategroup.php'">Create Group</button>
        
        <h2>My Groups</h2>
        <ul>
        <?php
            while ($row = mysqli_fetch_assoc($result)) {
                $group_id = $row['group_id'];
                $group_name = $row['group_name'];
                echo "<li><a href='#' onclick='openChat($group_id)'>$group_name</a></li>";
            }
        ?>
        </ul>
    </main>

    <div id="chat-box" class="chat-box">
        <div id="chat-history" class="chat-history"></div>
        <form id="message-form" class="message-form" onsubmit="return sendMessage()">
            <input id="message-input" type="text" name="message" placeholder="Type your message...">
            <input type="submit" value="Send">
        </form>
    </div>

    <script>
        let currentGroupId = null;

        function openChat(groupId) {
            currentGroupId = groupId;
            document.getElementById('chat-box').style.display = 'block';
            fetchChatHistory(groupId);
        }

        function fetchChatHistory(groupId) {
            fetch(`fetchChatHistory.php?group_id=${groupId}`)
                .then(response => {
                    if (response.ok) {
                        return response.text();
                    } else {
                        throw new Error(`Error fetching chat history. Status: ${response.status}`);
                    }
                })
                .then(chatHistoryHtml => {
                    console.log("Fetched chat history:", chatHistoryHtml);
                    document.getElementById('chat-history').innerHTML = chatHistoryHtml;
                })
                .catch(error => {
                    console.error("Error fetching chat history:", error);
                });
        }


        function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value.trim();
            if (message === '') {
                return false;
            }

            const formData = new FormData();
            formData.append('group_id', currentGroupId);
            formData.append('message', message);

            fetch('sendMessage.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Add this line to retrieve the response text
            .then(responseText => { // Modify this line to process the response text
                console.log('Response from sendMessage.php:', responseText); // Log the response text to the console
                fetchChatHistory(currentGroupId);
                messageInput.value = '';
            });

            return false;
        }



    </script>

</body>
</html>