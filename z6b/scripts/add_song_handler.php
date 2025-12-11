<?php
session_start();
require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idpl = $_POST['playlist_id'];
    $ids = $_POST['song_id'];

    try {
        $sql = "INSERT INTO playlistdatabase (idpl, ids) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idpl, $ids]);

        header('Location: ../index.php?page=my_playlists&msg=song_added');
    } catch (PDOException $e) {
        die("Błąd dodawania utworu: " . $e->getMessage());
    }
}
?>