<?php
// Konfiguracja połączenia
$host = '127.0.0.1';
$db_name = 'damskopb_z12';
$username = 'damskopb_z12';
$password = 'Lab12Password2025!';

// Połączenie MySQLi
$conn = new mysqli($host, $username, $password, $db_name);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode(["status" => "error", "message" => "Błąd połączenia: " . $conn->connect_error]));
}

// Ustawienie kodowania znaków
$conn->set_charset("utf8mb4");
?>