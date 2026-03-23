<?php
/**
 * Plik: pages/panel_admin.php
 * Cel: Zarządzanie kontami szkoleniowców i pracowników przez administratora.
 */
session_start();
require_once '../database/database.php';

// Weryfikacja uprawnień (rola admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Brak uprawnień. Zaloguj się jako administrator.");
}

// Obsługa usuwania
if (isset($_GET['delete_coach'])) {
    $pdo->prepare("DELETE FROM coach WHERE idc = ? AND login != 'admin'")->execute([$_GET['delete_coach']]);
    header("Location: panel_admin.php?msg=Usunięto coacha");
    exit();
}
if (isset($_GET['delete_pracownik'])) {
    $pdo->prepare("DELETE FROM pracownik WHERE idp = ?")->execute([$_GET['delete_pracownik']]);
    header("Location: panel_admin.php?msg=Usunięto pracownika");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-danger mb-4">
    <div class="container">
        <span class="navbar-brand">Panel Administracyjny Systemu E-learning</span>
        <a href="../database/logout_handler.php" class="btn btn-outline-light btn-sm">Wyloguj</a>
    </div>
</nav>

<div class="container">
    <?php if (isset($_GET['msg'])) echo "<div class='alert alert-info'>{$_GET['msg']}</div>"; ?>

    <div class="row">
        <!-- Lista Coachów -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Lista Szkoleniowców (Coach)</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>ID</th><th>Login</th><th>Akcja</th></tr></thead>
                        <tbody>
                            <?php
                            $coaches = $pdo->query("SELECT * FROM coach WHERE login != 'admin'")->fetchAll();
                            foreach ($coaches as $c) {
                                echo "<tr><td>{$c['idc']}</td><td>{$c['login']}</td>
                                      <td><a href='?delete_coach={$c['idc']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Na pewno?\")'>Usuń</a></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lista Pracowników -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Lista Kursantów (Pracownicy)</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead><tr><th>ID</th><th>Login</th><th>Akcja</th></tr></thead>
                        <tbody>
                            <?php
                            $workers = $pdo->query("SELECT * FROM pracownik")->fetchAll();
                            foreach ($workers as $w) {
                                echo "<tr><td>{$w['idp']}</td><td>{$w['login']}</td>
                                      <td><a href='?delete_pracownik={$w['idp']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Na pewno?\")'>Usuń</a></td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
