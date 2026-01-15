<?php
// z7/database.php - WERSJA DLA MYSQLI (Zadanie 7)
$host = '127.0.0.1';
$db_name = 'damskopb_z7';
$username = 'damskopb_...';
$password = '...';

// Uwaga: Tutaj używamy mysqli_connect, a nie PDO!
$polaczenie = mysqli_connect($host, $username, $password, $db_name);

if (!$polaczenie) {
    die("Błąd połączenia z MySQL: " . mysqli_connect_error());
}

// Ustawienie kodowania znaków
mysqli_set_charset($polaczenie, "utf8");
?>