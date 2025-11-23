<?php
session_start();

// Sprawdzenie sesji
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die('Brak dostępu.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folder_name'])) {

    $current_path = $_POST['current_path'] ?? '';

    // Zabezpieczenie ścieżki (zapobiega wyjściu poza katalog użytkownika)
    // Usuwamy kropki (..) żeby user nie wpisał np. "../../hack"
    $safe_path = str_replace('..', '', $current_path);
    $safe_name = str_replace(['/', '\\', '..'], '', $_POST['folder_name']); // Tylko nazwa, bez ścieżek

    // Pełna ścieżka na serwerze
    // Wychodzimy z 'scripts', potem z 'z5', do 'mycloud_files'
    $base_dir = '../../mycloud_files/' . $_SESSION['username'] . '/';
    $target_dir = $base_dir . $safe_path . '/' . $safe_name;

    // Normalizacja ścieżki (usuwa podwójne slashe)
    $target_dir = preg_replace('#/+#','/',$target_dir);

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
}

// Wracamy do dysku, zachowując aktualną ścieżkę
header("Location: ../index.php?page=home&path=" . urlencode($current_path));
exit();