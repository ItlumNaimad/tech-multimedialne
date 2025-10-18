<?php
declare(strict_types=1); // włączenie typowania zmiennych w PHP >=7
session_start(); // zapewnia dostęp do zmienny sesyjnych w danym pliku [cite: 325]

// Sprawdzamy, czy zmienna sesyjna 'loggedin' istnieje i ma wartość true [cite: 306, 326]
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Jeśli nie, przekieruj do strony logowania [cite: 327]
    header('Location: index3.php');
    exit(); // Zatrzymaj wykonywanie skryptu [cite: 329]
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Damian Skonieczny - Strona chroniona</title>
</head>
<body>
<h3>Witaj na stronie chronionej!</h3>
<p>Jesteś poprawnie zalogowany.</p>

<a href="logout.php">Wyloguj się</a>
</body>
</html>
