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

// Jeśli to admin i nie wybrano konkretnego odbiorcy, pobieramy wszystko
// Jeśli wybrano odbiorcę, filtrujemy rozmowę
if ($currentUser === 'admin') {
    if (empty($recipient)) {
        // Globalny podgląd dla admina (wszystkie wiadomości)
        $sql = "SELECT * FROM messages ORDER BY datetime ASC";
    } else {
        // Rozmowa admina z kimś lub podgląd konkretnej rozmowy
        $sql = "SELECT * FROM messages 
                WHERE (user = '$currentUser' AND recipient = '$recipient') 
                   OR (user = '$recipient' AND recipient = '$currentUser')
                ORDER BY datetime ASC";
    }
} else {
    // Zwykły użytkownik MUSI mieć odbiorcę
    if (empty($recipient)) {
        echo json_encode([]);
        exit();
    }
    $sql = "SELECT * FROM messages 
            WHERE (user = '$currentUser' AND recipient = '$recipient') 
               OR (user = '$recipient' AND recipient = '$currentUser')
            ORDER BY datetime ASC";
}

$result = mysqli_query($conn, $sql);
$messages = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($messages);
?>