<?php
/**
 * Plik: database/database.php
 * Cel: Konfiguracja połączenia z bazą danych przy użyciu interfejsu PDO.
 * Funkcjonalność: Tworzy globalny obiekt $pdo wykorzystywany w procesach logowania i rejestracji.
 * Wykorzystane biblioteki: PHP Data Objects (PDO).
 * Sposób działania: Definiuje parametry DSN (host, baza, charset) i tworzy instancję PDO z ustawionym raportowaniem błędów w trybie wyjątków.
 */

$host = '127.0.0.1';
$db_name = 'damskopb_z12';
$username = 'damskopb_z12';
$password = 'Lab12Password2025!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}
?>