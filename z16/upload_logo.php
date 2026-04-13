<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['admin_cms'])) {
    die("Brak uprawnień.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['logo'])) {
    $id_cms = $_SESSION['admin_cms'];
    $file = $_FILES['logo'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_name = 'logo_' . $id_cms . '_' . time() . '.' . $ext;
        
        if (move_uploaded_file($file['tmp_name'], $new_name)) {
            $stmt = $pdo->prepare("UPDATE cms SET logo_file = ? WHERE id_cms = ?");
            $stmt->execute([$new_name, $id_cms]);
        }
    }

    header("Location: index.php");
    exit;
}
?>
