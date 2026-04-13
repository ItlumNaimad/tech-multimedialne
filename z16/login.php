<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_cms = $_POST['id_cms'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id_cms = ? AND username = ?");
    $stmt->execute([$id_cms, $username]);
    $admin = $stmt->fetch();

    if ($admin && $password === $admin['password']) { // W realnym systemie użyj password_verify
        $_SESSION['admin_cms'] = $id_cms;
        $_SESSION['admin_user'] = $username;

        // Zapis do historii logowania
        $stmt_hist = $pdo->prepare("INSERT INTO login_history (id_cms, username, ip_address) VALUES (?, ?, ?)");
        $stmt_hist->execute([$id_cms, $username, $_SERVER['REMOTE_ADDR']]);

        header("Location: index.php");
        exit;
    } else {
        echo "<script>alert('Błędne dane logowania!'); window.location.href='index.php';</script>";
    }
}
?>
