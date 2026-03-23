<?php
/**
 * Plik: db_connect.php
 * Cel: Konfiguracja połączenia z bazą danych przy użyciu MySQLi.
 * Funkcjonalność: Tworzy obiekt $conn dla skryptów API i statystyk.
 * Wykorzystane biblioteki: MySQLi.
 */
require_once 'config.php';

// Połączenie MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Sprawdzenie połączenia
if ($conn->connect_error) {
    header('Content-Type: application/json');
    die(json_encode(["status" => "error", "message" => "Błąd połączenia: " . $conn->connect_error]));
}

// Ustawienie kodowania znaków
$conn->set_charset(DB_CHARSET);
?>
