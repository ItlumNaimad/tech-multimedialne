<?php
/**
 * Plik: database/register_handler.php
 * Cel: Obsługa procesu zakładania nowego konta użytkownika.
 * Funkcjonalność: Walidacja danych formularza i zapis w bazie.
 * Wykorzystane biblioteki: PDO.
 * Sposób działania: Porównuje hasła, sprawdza kompletność danych, haszuje hasło algorytmem domyślnym PHP (BCRYPT/Argon2) i wykonuje INSERT do tabeli 'users'.
 */
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    // --- Walidacja hasła i pól ---
    if ($password !== $password_repeat) {
        die("Hasła nie są identyczne. Wróć i spróbuj ponownie.");
    }
    if (empty($username) || empty($password)) {
        die("Nazwa użytkownika i hasło nie mogą być puste.");
    }

    // --- Haszowanie Hasła ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        header("Location: " . $_POST['redirect_url']); 
        exit();

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            die("Ta nazwa użytkownika jest już zajęta. Wybierz inną.");
        } else {
            die("Błąd podczas rejestracji: " . $e->getMessage());
        }
    }
} else {
    echo "Nieautoryzowany dostęp.";
}
?>