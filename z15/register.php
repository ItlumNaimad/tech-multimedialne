<?php
session_start();
require_once 'db_connect.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $nazwisko = $_POST['nazwisko'];
    $pass = $_POST['pass'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO uzytkownicy (login, haslo, nazwisko, typ) VALUES (?, ?, ?, 'klient')");
        $stmt->execute([$login, $pass, $nazwisko]);
        $success = true;
    } catch (PDOException $e) {
        $error = "Ten login jest już zajęty lub wystąpił błąd bazy danych.";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rejestracja Klienta - WHS CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center">Zarejestruj się jako Klient</h3>
                    <?php if($success): ?>
                        <div class="alert alert-success text-center">
                            Konto utworzone pomyślnie! <br>
                            Możesz teraz przejść do <a href="login.php?typ=klient">logowania</a>.
                        </div>
                    <?php else: ?>
                        <?php if($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Login</label>
                                <input type="text" name="login" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Imię i Nazwisko / Nazwa Firmy</label>
                                <input type="text" name="nazwisko" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Hasło (pass3 dla testu)</label>
                                <input type="password" name="pass" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-secondary w-100">Zarejestruj</button>
                            <div class="mt-3 text-center">
                                <a href="index.php">Wróć do strony głównej</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
