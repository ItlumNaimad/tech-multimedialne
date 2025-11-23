<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_GET['file'])) {
    die('Brak dostępu.');
}

$current_path = $_GET['path'] ?? '';
$file_to_download = $_GET['file'];

$safe_path = str_replace('..', '', $current_path);
$safe_file = str_replace('..', '', $file_to_download);

$base_dir = '../../mycloud_files/' . $_SESSION['username'] . '/';
$full_path = $base_dir . $safe_path . '/' . $safe_file;
$full_path = preg_replace('#/+#','/',$full_path);

if (file_exists($full_path) && !is_dir($full_path)) {
    // Nagłówki wymuszające pobieranie
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($full_path).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($full_path));
    readfile($full_path);
    exit;
} else {
    die("Plik nie istnieje.");
}