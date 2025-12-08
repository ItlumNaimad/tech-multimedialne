<?php
session_start();
require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['music_file'])) {

    $title = $_POST['title'];
    $musician = $_POST['musician'];
    $idmt = $_POST['idmt'];
    $idu = $_SESSION['user_id'];

    // Ścieżka do folderu z muzyką
    $upload_dir = '../media/music/';

    // Unikalna nazwa pliku
    $file_name = time() . '_' . basename($_FILES['music_file']['name']);
    $target_file = $upload_dir . $file_name;

    // Przesyłanie
    if (move_uploaded_file($_FILES['music_file']['tmp_name'], $target_file)) {
        // SQL Insert
        $sql = "INSERT INTO song (title, musician, idu, filename, idmt) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $musician, $idu, $file_name, $idmt]);

        header('Location: ../index.php?page=home&msg=success');
    } else {
        die("Błąd przesyłania pliku. Upewnij się, że folder media/music ma uprawnienia 777.");
    }
}
?>