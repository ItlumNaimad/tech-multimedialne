<?php
// Używamy sesji, więc musimy ją uruchomić na samej górze
session_start();

// 1. Dołączamy nasz bezpieczny plik bazy danych
require_once 'database.php';

// 2. Sprawdzamy, czy formularz został wysłany
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password_form = $_POST['password']; // Hasło z formularza

    // 3. Bezpieczne zapytanie (Prepared Statement)
    // Pobieramy użytkownika TYLKO po nazwie
    $sql = "SELECT * FROM users WHERE username = :username";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(); // Pobierz wiersz użytkownika

        // 4. Weryfikacja
        // Sprawdzamy, czy użytkownik istnieje ORAZ czy hasło się zgadza
        if ($user && password_verify($password_form, $user['password'])) {

            // Hasło jest poprawne!
            // 5. Uruchamiamy sesję
            session_regenerate_id(); // Ważne dla bezpieczeństwa
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];

            // Przekierowujemy do "tajnego" panelu
            header("Location: ../z1/panel.php");
            exit();

        } else {
            // Zły login lub hasło
            die("Nieprawidłowa nazwa użytkownika lub hasło.");
        }

    } catch (PDOException $e) {
        die("Błąd logowania: " . $e->getMessage());
    }

} else {
    die("Nieautoryzowany dostęp.");
}
?>
