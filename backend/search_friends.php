<?php
    // If form is submitted, search for user
    if (isset($_POST['submit'])) {
        $search_term = mysqli_real_escape_string($db_connection, $_POST['search']);
        $query = "SELECT * FROM `User Accounts` WHERE username='$search_term'";
        $result = mysqli_query($db_connection, $query);

        // Create an empty array to store the search results
        $search_results = [];

        // If user found, add user's information to the search results array
        if (mysqli_num_rows($result) > 0) {
            while ($user = mysqli_fetch_assoc($result)) {
                $search_results[] = $user;
            }
        }

        // Send the search results as JSON response
        header('Content-Type: application/json');
        echo json_encode($search_results);
        exit();
    }

    // If send friend request button is clicked, add request to Friend_Requests table
    if (isset($_POST['send_request'])) {
        $receiver_id = mysqli_real_escape_string($db_connection, $_POST['receiver_id']);

        $query = "INSERT INTO `Friend_Requests` (sender_id, receiver_id, status) VALUES ('$current_user_id', '$receiver_id', 'pending')";
        mysqli_query($db_connection, $query);

        echo "<p>Friend request sent.</p>";
        exit();
    }
?>
