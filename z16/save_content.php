<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['admin_cms'])) {
    die("Brak uprawnień.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cms = $_SESSION['admin_cms'];
    $page = $_POST['page'];
    $content = $_POST['content'];

    $column = '';
    switch($page) {
        case 'about': $column = 'about_company'; break;
        case 'contact': $column = 'contact'; break;
        case 'offer': $column = 'offer'; break;
    }

    if ($column) {
        $stmt = $pdo->prepare("UPDATE cms SET $column = ? WHERE id_cms = ?");
        $stmt->execute([$content, $id_cms]);
    }

    header("Location: index.php?page=$page");
    exit;
}
?>
