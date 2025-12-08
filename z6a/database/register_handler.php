<?php
// 1. Włączamy wyświetlanie błędów (ABY NIE BYŁO ERROR 500)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Poprawna nazwa pliku bazy dla z6a
require_once 'database_z6a.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password_repeat'];

    // Walidacja
    if ($password !== $password_repeat) die("Hasła nie są identyczne.");
    if (empty($username) || empty($password)) die("Uzupełnij wszystkie pola.");

    // Haszowanie
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // (Opcjonalnie: Tutaj byłby kod od awatara, ale na razie go pomińmy dla testu)
    $avatar_path = null;

    // 3. Zapis do bazy
    // Upewnij się, że tabela 'users' w bazie 'damskopb_myspotify' ma kolumnę 'avatar_path'
    // Jeśli skopiowałeś tabelę z z1/z2, mogłeś nie mieć tej kolumny.
    // Jeśli nie masz, usuń ", avatar_path" i ":avatar_path" z zapytania.
    $sql = "INSERT INTO users (username, password, avatar_path) VALUES (:username, :password, :avatar_path)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':avatar_path' => $avatar_path
        ]);

        // Przekierowanie po sukcesie
        header("Location: ../index.php?page=logowanie&msg=registered");
        exit();

    } catch (PDOException $e) {
        die("Błąd SQL podczas rejestracji: " . $e->getMessage());
    }
}
?>