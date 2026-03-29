<?php
/**
 * Plik: database/register_handler.php
 * Cel: Obsługa rejestracji nowych pracowników (kursantów).
 */
session_start();
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_repeat = $_POST['password_repeat'] ?? '';
    $role = $_POST['role'] ?? 'pracownik';

    if ($password !== $password_repeat) {
        die("Hasła nie są identyczne.");
    }
    if (empty($username) || empty($password)) {
        die("Pola nie mogą być puste.");
    }

    try {
        if ($role === 'coach') {
            $stmt = $pdo->prepare("INSERT INTO coach (login, haslo) VALUES (:login, :haslo)");
        } else {
            $stmt = $pdo->prepare("INSERT INTO pracownik (login, haslo) VALUES (:login, :haslo)");
        }
        $stmt->execute(['login' => $username, 'haslo' => $password]);

        // Przekierowanie zależne od tego, kto dodaje użytkownika
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            header("Location: ../pages/panel_admin.php?msg=Użytkownik ($role) dodany pomyślnie.");
        } else {
            header("Location: ../pages/logowanie.php?registered=1");
        }
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
