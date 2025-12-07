<?php
session_start();
require_once '../database/database_z6a.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['music_file'])) {

    $title = $_POST['title'];
    $musician = $_POST['musician'];
    $idmt = $_POST['idmt'];
    $idu = $_SESSION['user_id'];

    $upload_dir = '../media/music/';
    // Unikalna nazwa pliku: timestamp_nazwa.mp3
    $file_name = time() . '_' . basename($_FILES['music_file']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($_FILES['music_file']['tmp_name'], $target_file)) {
        // Zapis do bazy
        $sql = "INSERT INTO song (title, musician, idu, filename, idmt) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $musician, $idu, $file_name, $idmt]);

        header('Location: ../index.php?page=home&msg=success');
    } else {
        die("Błąd przesyłania pliku. Sprawdź uprawnienia folderu media/music.");
    }
}