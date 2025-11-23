<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_GET['file'])) {
    die('Brak dostępu.');
}

$current_path = $_GET['path'] ?? '';
$file_to_delete = $_GET['file'];

// Zabezpieczenia
$safe_path = str_replace('..', '', $current_path);
$safe_file = str_replace('..', '', $file_to_delete);

$base_dir = '../../mycloud_files/' . $_SESSION['username'] . '/';
$full_path = $base_dir . $safe_path . '/' . $safe_file;
$full_path = preg_replace('#/+#','/',$full_path);

if (file_exists($full_path)) {
    if (is_dir($full_path)) {
        // Usuwanie folderu (musi być pusty, zgodnie z instrukcją [cite: 1176])
        @rmdir($full_path);
    } else {
        // Usuwanie pliku
        unlink($full_path);
    }
}

header("Location: ../index.php?page=home&path=" . urlencode($current_path));
exit();