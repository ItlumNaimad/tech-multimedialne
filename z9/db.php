<?php
// Włączamy raportowanie błędów, aby zamiast błędu 500 zobaczyć konkretną przyczynę
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Wyłączamy wyjątki mysqli (częsta przyczyna błędu 500 na PHP 8.1+)
mysqli_report(MYSQLI_REPORT_OFF);

// DANE POŁĄCZENIA - upewnij się, że są identyczne z tymi w panelu hostingu
$host = 'localhost'; 
$user = 'root'; // Na hostingu to zazwyczaj coś jak 'damskopbs_user'
$pass = '';     // Na hostingu hasło jest wymagane
$dbname = 'tech_multimedialne'; // Na hostingu zazwyczaj coś jak 'damskopbs_db'

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

// Używamy utf8 (zgodnie z Twoim exportem SQL: utf8mb3)
mysqli_set_charset($conn, "utf8");
?>