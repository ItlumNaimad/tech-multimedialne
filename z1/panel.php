<?php
// Uruchom sesję, aby mieć dostęp do zmiennych
session_start();

// Sprawdzamy, czy użytkownik jest zalogowany
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: logowanie.php'); // Wyrzuć do naszego bezpiecznego logowania
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Damian Skonieczny - Panel</title>
</head>
<body>
<h3>Witaj w bezpiecznym panelu, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
<p>Jesteś poprawnie zalogowany za pomocą PDO i password_verify().</p>

<a href="logout.php">Wyloguj się</a>
</body>
</html>
