<?php
session_start();
require_once '../database/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['film_file'])) {

    $title = $_POST['title'];
    $director = $_POST['director'];
    $idft = $_POST['idft'];
    $idu = $_SESSION['user_id'];

    // --- STARA WERSJA (mogła mylić ścieżki) ---
    // $upload_dir = '../media/films/';

    // --- NOWA WERSJA (Ścieżka absolutna - niezawodna) ---
    // __DIR__ to folder 'scripts'. Wychodzimy z niego (/../) do 'z6a', potem do 'media/films/'
    $upload_dir = __DIR__ . '/../media/films/';
    // Auto-naprawa folderu
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    // Unikalna nazwa pliku
    $file_name = time() . '_' . basename($_FILES['film_file']['name']);
    $target_file = $upload_dir . $file_name;

    // Przesyłanie
    if (move_uploaded_file($_FILES['film_file']['tmp_name'], $target_file)) {
        // SQL Insert
        $sql = "INSERT INTO film (title, director, idu, filename, idft) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $director, $idu, $file_name, $idft]);

        header('Location: ../index.php?page=home&msg=success');
    } else {
        die("Błąd przesyłania pliku. Upewnij się, że folder media/films ma uprawnienia 777.");
    }
}
?>