<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    http_response_code(403);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_SESSION['username'];
    $recipient = mysqli_real_escape_string($conn, $_POST['recipient']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    $file_path = "";
    $file_type = "";

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $target_dir = "upload/";
        // Ensure directory exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["file"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
            
            // Determine file type
            if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $file_type = 'image';
            } elseif ($file_extension == 'mp4') {
                $file_type = 'video';
            } elseif ($file_extension == 'mp3') {
                $file_type = 'audio';
            } elseif ($file_extension == 'pdf') {
                $file_type = 'pdf';
            } else {
                $file_type = 'file';
            }
        }
    }

    $sql = "INSERT INTO messages (message, user, recipient, file_path, file_type) 
            VALUES ('$message', '$user', '$recipient', '$file_path', '$file_type')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
}
?>