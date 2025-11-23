<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die('Brak dostępu.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileToUpload'])) {

    $current_path = $_POST['current_path'] ?? '';
    $safe_path = str_replace('..', '', $current_path);

    // Ścieżka bazowa użytkownika
    $base_dir = '../../mycloud_files/' . $_SESSION['username'] . '/';
    $target_dir = $base_dir . $safe_path . '/';
    $target_dir = preg_replace('#/+#','/',$target_dir); // Czyścimy slashe

    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    // Próba przesłania
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // Sukces
    } else {
        // Błąd (w prawdziwej aplikacji można dodać komunikat do sesji)
    }
}

header("Location: ../index.php?page=home&path=" . urlencode($current_path));
exit();