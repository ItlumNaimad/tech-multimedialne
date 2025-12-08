<?php
$host = '127.0.0.1';
// UPEWNIJ SIĘ, ŻE TO SĄ POPRAWNE DANE Z DIRECTADMINA:
$db_name = 'damskopb_myspotify';
$username = 'damskopb_myspotify';
$password = 'T2zjgFa2Ew7nLaR8Jtwr!'; // <-- Zmień hasło w panelu i wpisz je tu
$charset = 'utf8mb4';

// Opcje konfiguracyjne dla PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// DSN (Data Source Name) - określa typ i lokalizację bazy danych
$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

try {
    // Utworzenie obiektu PDO (reprezentującego połączenie z bazą)
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // W prawdziwej aplikacji błąd należałoby zalogować, a nie wyświetlać użytkownikowi
    //throw new PDOException($e->getMessage(), (int)$e->getCode());
    die("<h3>Błąd połączenia z bazą danych:</h3> " . $e->getMessage());
}
?>