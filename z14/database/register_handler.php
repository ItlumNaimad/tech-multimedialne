<?php
/**
 * Plik: database/register_handler.php
 * Cel: Obsługa rejestracji nowych pracowników (kursantów).
 */
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_repeat = $_POST['password_repeat'] ?? '';

    if ($password !== $password_repeat) {
        die("Hasła nie są identyczne.");
    }
    if (empty($username) || empty($password)) {
        die("Pola nie mogą być puste.");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO pracownik (login, haslo) VALUES (:login, :haslo)");
        $stmt->execute(['login' => $username, 'haslo' => $password]);

        // Przekierowanie do strony logowania po sukcesie
        header("Location: ../pages/logowanie.php?registered=1");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            die("Ta nazwa użytkownika jest już zajęta.");
        } else {
            die("Błąd podczas rejestracji: " . $e->getMessage());
        }
    }
} else {
    echo "Nieautoryzowany dostęp.";
}
?>
