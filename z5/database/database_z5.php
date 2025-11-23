<?php

/// Ustawienia połączenia z bazą danych dla Z5
$host = '127.0.0.1';
$db_name = 'nazwa_bazy_dla_z5'; // <--- NOWA BAZA DANYCH
$username = 'uzytkownik_z5';   // <--- NOWY UŻYTKOWNIK
$password = 'haslo_z5';         // <--- NOWE HASŁO
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
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Od tego momentu, zmienna $pdo jest dostępna i reprezentuje aktywne połączenie z bazą.