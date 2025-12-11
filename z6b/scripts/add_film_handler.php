<?php
session_start();
require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idpl = $_POST['playlist_id'];

    // ZMIANA 1: Odbieramy film_id
    $idf = $_POST['film_id'];

    try {
        // ZMIANA 2: Wstawiamy do kolumny 'idf' (nie 'ids')
        // Tabela playlistdatabase w z6b ma kolumny: idpldb, idpl, idf
        $sql = "INSERT INTO playlistdatabase (idpl, idf) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idpl, $idf]);

        header('Location: ../index.php?page=my_playlists&msg=film_added');
    } catch (PDOException $e) {
        die("Błąd dodawania filmu: " . $e->getMessage());
    }
}
?>