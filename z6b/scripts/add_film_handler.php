<?php
session_start();
// Włącz raportowanie wszystkich błędów na czas debugowania
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdź, czy dane z formularza docierają
    if (!isset($_POST['playlist_id']) || !isset($_POST['film_id'])) {
        die("Błąd: Brakujące dane z formularza (playlist_id lub film_id).");
    }

    $idpl = $_POST['playlist_id'];
    $idf = $_POST['film_id'];

    // Sprawdź, czy wartości nie są puste
    if (empty($idpl) || empty($idf)) {
        die("Błąd: ID playlisty lub filmu jest puste.");
    }

    echo "Próba dodania filmu o ID: " . htmlspecialchars($idf) . " do playlisty o ID: " . htmlspecialchars($idpl) . "<br>";

    try {
        // Sprawdź, czy film o danym ID istnieje
        $stmt_check_film = $pdo->prepare("SELECT COUNT(*) FROM film WHERE idf = ?");
        $stmt_check_film->execute([$idf]);
        if ($stmt_check_film->fetchColumn() == 0) {
            die("Błąd krytyczny: Film o ID " . htmlspecialchars($idf) . " nie istnieje w tabeli 'film'!");
        }

        // Sprawdź, czy playlista o danym ID istnieje
        $stmt_check_playlist = $pdo->prepare("SELECT COUNT(*) FROM playlistname WHERE idpl = ?");
        $stmt_check_playlist->execute([$idpl]);
        if ($stmt_check_playlist->fetchColumn() == 0) {
            die("Błąd krytyczny: Playlista o ID " . htmlspecialchars($idpl) . " nie istnieje w tabeli 'playlistname'!");
        }

        // Jeśli wszystko się zgadza, wykonaj INSERT
        $sql = "INSERT INTO playlistdatabase (idpl, idf) VALUES (?, ?)";
        echo "Wykonywanie zapytania: <pre>" . htmlspecialchars($sql) . "</pre>";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idpl, $idf]);

        echo "Sukces! Film został dodany do playlisty.";
        // Po udanym debugowaniu, odkomentuj poniższą linię i usuń echo
        // header('Location: ../index.php?page=my_playlists&msg=film_added');
        exit();

    } catch (PDOException $e) {
        // Błąd związany z bazą danych (np. naruszenie klucza obcego, zła nazwa tabeli)
        die("Błąd bazy danych przy dodawaniu filmu: " . $e->getMessage());
    } catch (Throwable $t) {
        // Inne błędy (np. błąd w kodzie PHP)
        die("Wystąpił nieoczekiwany błąd: " . $t->getMessage());
    }
} else {
    die("Niedozwolona metoda dostępu.");
}
?>