<?php
// Plik obsługujący dodawanie hosta [cite: 142]
session_start();
require_once '../src/database.php';

// Sprawdzamy sesję i czy dane zostały wysłane
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && $_SERVER["REQUEST_METHOD"] == "POST") {

    $host = $_POST['host'] ?? null;
    $port = $_POST['port'] ?? null;
    $user_id = $_SESSION['user_id']; // Zapisujemy hosta dla zalogowanego usera

    if ($host && $port) {
        try {
            // Wstawiamy nowy rekord
            $sql = "INSERT INTO domeny (host, port, user_id) VALUES (:host, :port, :user_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['host' => $host, 'port' => (int)$port, 'user_id' => $user_id]);

            // Wracamy do strony monitora
            header("Location: index.php?page=home");
            exit();

        } catch (PDOException $e) {
            die("Błąd bazy danych: " . $e->getMessage());
        }
    } else {
        die("Brakujące dane.");
    }
} else {
    die("Nieautoryzowany dostęp.");
}
?>
