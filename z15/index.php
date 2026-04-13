<?php
session_start();
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>WHS CRM - System Obsługi Klienta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .hero { padding: 60px 0; text-align: center; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">WHS CRM</a>
        <div class="navbar-text">
            <?php if(isset($_SESSION['user'])): ?>
                Zalogowany: <strong><?= htmlspecialchars($_SESSION['user']['nazwisko']) ?></strong> (<?= $_SESSION['user']['typ'] ?>)
                <a href="logout.php" class="btn btn-sm btn-outline-danger ms-2">Wyloguj</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container hero">
    <h1>WHS CRM - Web Hosting Services</h1>
    <p class="lead">System zarządzania relacjami z klientami dla Twojego hostingu.</p>
    
    <?php if(!isset($_SESSION['user'])): ?>
    <div class="row mt-5 justify-content-center">
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Dla Klienta</h5>
                    <p class="card-text">Zarejestruj się lub zaloguj, aby uzyskać pomoc technologiczną.</p>
                    <a href="login.php?typ=klient" class="btn btn-primary">Zaloguj jako Klient</a>
                    <a href="register.php" class="btn btn-outline-secondary mt-2">Rejestracja</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Dla Pracownika</h5>
                    <p class="card-text">Zaloguj się do panelu obsługi zgłoszeń.</p>
                    <a href="login.php?typ=pracownik" class="btn btn-success">Zaloguj jako Pracownik</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Administracja</h5>
                    <p class="card-text">Statystyki i zarządzanie systemem.</p>
                    <a href="login.php?typ=admin" class="btn btn-dark">Panel Admina</a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="mt-4">
            <?php if($_SESSION['user']['typ'] == 'klient') echo '<a href="client.php" class="btn btn-primary btn-lg">Przejdź do Twojego Panelu</a>'; ?>
            <?php if($_SESSION['user']['typ'] == 'pracownik') echo '<a href="worker.php" class="btn btn-success btn-lg">Panel Obsługi Zgłoszeń</a>'; ?>
            <?php if($_SESSION['user']['typ'] == 'admin') echo '<a href="admin.php" class="btn btn-dark btn-lg">Statystyki Systemu</a>'; ?>
        </div>
    <?php endif; ?>
</div>

<footer class="mt-5 text-center text-muted">
    <p>&copy; 2026 WHS CRM - Zadanie 15</p>
</footer>
</body>
</html>
