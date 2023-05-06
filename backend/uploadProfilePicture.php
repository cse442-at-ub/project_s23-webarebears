<?php
session_start();
$user_id = $_SESSION['user_id'];

require('server.php');
header('Content-Type: application/json');

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $file = $_FILES['profile_pic'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $allowed_ext = array('jpg', 'jpeg', 'png');

    if (in_array(strtolower($ext), $allowed_ext)) {
        $image_path = "images/{$user_id}.{$ext}";
        $destination  = 'images/' . $user_id . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $sql = "INSERT INTO Profile_Pictures (user_id, image_path) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE image_path = ?";
            $stmt = $db_connection->prepare($sql);
            $stmt->bind_param("iss", $user_id, $image_path, $image_path);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }         
        else {
            echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file.', 'debug' => ['destination' => $destination]]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid file extension.', 'debug' => ['file_ext' => $ext]]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No file uploaded or upload error.',
        'details' => [
            'files' => $_FILES,
            'profile_pic_error' => $_FILES['profile_pic']['error']
        ],
        'debug' => ['session_user_id' => $user_id]
    ]);
}
?>
