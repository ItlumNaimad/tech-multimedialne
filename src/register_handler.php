<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    $avatar_path_to_db = null; // Domyślnie brak awatara (NULL)

    // --- Walidacja hasła i pól (bez zmian) ---
    if ($password !== $password_repeat) {
        die("Hasła nie są identyczne. Wróć i spróbuj ponownie.");
    }
    if (empty($username) || empty($password)) {
        die("Nazwa użytkownika i hasło nie mogą być puste.");
    }

    // --- NOWA SEKCJA: Obsługa Uploadu Awatara ---
    // Sprawdzamy, czy plik został wysłany i czy nie ma błędu
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {

        $file = $_FILES['avatar'];
        $max_filesize_mb = 2;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $upload_dir = '../uploads/'; // Względna do folderu 'src'

        // 1. Sprawdzenie rozmiaru
        if ($file['size'] > $max_filesize_mb * 1024 * 1024) {
            die("Plik jest za duży. Maksymalny rozmiar to 2MB.");
        }

        // 2. Sprawdzenie typu pliku
        if (!in_array($file['type'], $allowed_types)) {
            die("Niedozwolony typ pliku. Dozwolone są tylko JPG, PNG i GIF.");
        }

        // 3. Stworzenie unikalnej nazwy pliku, aby uniknąć nadpisywania
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid('avatar_', true) . '.' . $file_extension;
        $upload_path = $upload_dir . $unique_filename;

        // 4. Przeniesienie pliku z folderu tymczasowego
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Sukces! Ustaw ścieżkę do zapisu w bazie
            // Zapisujemy ścieżkę względną do GŁÓWNEGO FOLDERU (public_html)
            $avatar_path_to_db = 'uploads/' . $unique_filename;
        } else {
            die("Błąd podczas przenoszenia pliku. Spróbuj ponownie.");
        }
    }
    // Jeśli plik nie został wysłany (jest opcjonalny), po prostu zostawiamy $avatar_path_to_db jako NULL


    // --- Haszowanie Hasła (bez zmian) ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // --- ZMODYFIKOWANY ZAPIS DO BAZY ---
    // Dodaliśmy kolumnę `avatar_path` do zapytania
    $sql = "INSERT INTO users (username, password, avatar_path) VALUES (:username, :password, :avatar_path)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':avatar_path', $avatar_path_to_db); // Powiąż ścieżkę awatara
        $stmt->execute();

        // Inteligentne przekierowanie
        if (isset($_POST['redirect_url']) && !empty($_POST['redirect_url'])) {
            // Jeśli formularz podał nam, dokąd wrócić, użyj tego
            header("Location: " . $_POST['redirect_url']);
        } else {
            // Jeśli nie (dla bezpieczeństwa), użyj domyślnej lokalizacji z z2
            header("Location: ../z2/index.php?page=panel");
        }
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