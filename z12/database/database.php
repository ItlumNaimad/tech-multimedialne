<?php

$host = '127.0.0.1';
$db_name = 'damskopb_z12'; // <--- Twoja nazwa z DirectAdmin (Dostosuj jeśli inna)
$username = 'damskopb_z12'; // <--- Twój user z DirectAdmin
$password = 'Lab12Password2025!';  // <--- Hasło z Kroku 1
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą: " . $e->getMessage());
}
?>