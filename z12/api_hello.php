<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// Odbieramy parametr 'message' (GET lub POST)
$message = $_REQUEST['message'] ?? null;

if ($message === null) {
    echo json_encode(["status" => "error", "message" => "Brak parametru 'message'"]);
    exit();
}

// Zabezpieczenie Prepared Statement
$stmt = $conn->prepare("INSERT INTO hello_arduino (message) VALUES (?)");
$stmt->bind_param("s", $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Wiadomość została zapisana", "id" => $conn->insert_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Błąd zapisu: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>