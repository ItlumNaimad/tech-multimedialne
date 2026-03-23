<?php
/**
 * Plik: database/database.php
 * Cel: Konfiguracja połączenia z bazą danych przy użyciu interfejsu PDO.
 * Funkcjonalność: Tworzy globalny obiekt $pdo wykorzystywany w procesach logowania i rejestracji.
 * Wykorzystane biblioteki: PHP Data Objects (PDO).
 */
require_once __DIR__ . '/../config.php';

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}
?>
