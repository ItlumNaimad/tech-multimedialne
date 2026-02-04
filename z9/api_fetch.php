<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$currentUser = $_SESSION['username'];
$recipient = isset($_GET['recipient']) ? mysqli_real_escape_string($conn, $_GET['recipient']) : '';

if (empty($recipient)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No recipient specified']);
    exit();
}

// Admin sees everything in that conversation, 
// regular user sees only messages where they are sender or recipient in this specific conversation
if ($currentUser === 'admin') {
    // If admin is chatting with someone, or just observing a conversation between two people?
    // The request says "Czat powinien być inny na każdego użytkownika" and "Administrator widzi wszystko".
    // Let's assume for admin we show the conversation between chosen two people or all messages to/from that person.
    // However, usually admin sees EVERYTHING if they are just "admin", but here they are also a user.
    // Let's stick to the conversation logic: current user (even admin) vs recipient.
    $sql = "SELECT * FROM messages 
            WHERE (user = '$currentUser' AND recipient = '$recipient') 
               OR (user = '$recipient' AND recipient = '$currentUser')
            ORDER BY datetime ASC";
} else {
    $sql = "SELECT * FROM messages 
            WHERE ((user = '$currentUser' AND recipient = '$recipient') 
               OR (user = '$recipient' AND recipient = '$currentUser'))
            ORDER BY datetime ASC";
}

// Special case: if recipient is "all" (for admin broadcast) or if admin wants to see EVERYTHING (global log)
// But based on the UI flow, it's person-to-person.

$result = mysqli_query($conn, $sql);
$messages = [];

while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

header('Content-Type: application/json');
echo json_encode($messages);
?>