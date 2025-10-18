<?php

// Dołączamy nasz plik z połączeniem do bazy danych
require_once 'database.php';

// Sprawdzamy, czy formularz został wysłany metodą POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Odbieramy dane z formularza
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    // --- 1. Podstawowa Walidacja ---
    // Sprawdzamy, czy hasła są identyczne (zgodnie z instrukcją [cite: 344])
    if ($password !== $password_repeat) {
        die("Hasła nie są identyczne. Wróć i spróbuj ponownie.");
    }

    // Sprawdzamy, czy pola nie są puste (chociaż `required` w HTML już to robi)
    if (empty($username) || empty($password)) {
        die("Nazwa użytkownika i hasło nie mogą być puste.");
    }

    // --- 2. Haszowanie Hasła (Dobra Praktyka) ---
    // NIGDY nie przechowujemy haseł jako czysty tekst! [zamiast: 54]
    // Używamy bezpiecznej, wbudowanej funkcji PHP.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // --- 3. Zapis do Bazy Danych (Bezpiecznie z PDO) ---
    // Zamiast niebezpiecznego INSERT z instrukcji[cite: 345]...
    // Używamy PRZYGOTOWANYCH ZAPYTAŃ (Prepared Statements), aby chronić się przed SQL Injection

    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

    try {
        // Przygotuj zapytanie
        $stmt = $pdo->prepare($sql);

        // "Wstrzyknij" zmienne do zapytania w bezpieczny sposób
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);

        // Wykonaj zapytanie
        $stmt->execute();

        echo "Rejestracja pomyślna! Możesz się teraz zalogować.";
        // W przyszłości przekierujemy użytkownika do strony logowania
        // header("Location: ../z1/logowanie.php");

    } catch (PDOException $e) {
        // Obsługa błędu - np. jeśli użytkownik już istnieje (dzięki `UNIQUE` w tabeli)
        if ($e->getCode() == 23000) {
            die("Ta nazwa użytkownika jest już zajęta. Wybierz inną.");
        } else {
            // Inny błąd bazy danych
            die("Błąd podczas rejestracji: " . $e->getMessage());
        }
    }
} else {
    // Jeśli ktoś wszedł na ten plik bezpośrednio przez URL, a nie przez formularz
    echo "Nieautoryzowany dostęp.";
}
