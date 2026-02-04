<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$currentUser = $_SESSION['username'];
$isAdmin = ($currentUser === 'admin');

if ($isAdmin) {
    $sql = "SELECT * FROM messages ORDER BY datetime ASC";
} else {
    $sql = "SELECT * FROM messages 
            WHERE user = '$currentUser' OR recipient = '$currentUser' 
            ORDER BY datetime ASC";
}

$result = mysqli_query($conn, $sql);
$messages = [];

while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

header('Content-Type: application/json');
echo json_encode($messages);
?>