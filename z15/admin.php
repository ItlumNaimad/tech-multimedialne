<?php
session_start();
require_once 'db_connect.php';
require_once 'functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['typ'] != 'admin') {
    header('Location: login.php?typ=admin'); exit;
}

// Statystyki ogólne
$total_tickets = $pdo->query("SELECT COUNT(*) FROM zgloszenia")->fetchColumn();
$total_replies = $pdo->query("SELECT COUNT(*) FROM wiadomosci w JOIN uzytkownicy u ON w.id_autora = u.id WHERE u.typ = 'pracownik'")->fetchColumn();

// Statystyki pracowników
$worker_stats = $pdo->query("
    SELECT 
        u.id, 
        u.nazwisko, 
        COUNT(DISTINCT z.id) as tickets_handled,
        COUNT(DISTINCT w.id) as replies_count,
        AVG(z.ocena) as avg_rating
    FROM uzytkownicy u
    LEFT JOIN zgloszenia z ON u.id = z.id_pracownika
    LEFT JOIN wiadomosci w ON u.id = w.id_autora AND u.typ = 'pracownik'
    WHERE u.typ = 'pracownik'
    GROUP BY u.id
")->fetchAll();

// Średnia ilość obsłużonych ticketów dla wyznaczenia ikon tempa
$avg_tickets_handled = 0;
if (count($worker_stats) > 0) {
    $sum = 0;
    foreach($worker_stats as $ws) $sum += $ws['tickets_handled'];
    $avg_tickets_handled = $sum / count($worker_stats);
}

// Statystyki zagadnień
$topic_stats = $pdo->query("
    SELECT zag.nazwa, COUNT(z.id) as total
    FROM zagadnienia zag
    LEFT JOIN zgloszenia z ON zag.id = z.id_zagadnienia
    GROUP BY zag.id
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Administratora - WHS CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stat-card { border-radius: 15px; border: none; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-5px); }
        .icon-tempo { font-size: 1.5rem; }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow">
    <div class="container">
        <a class="navbar-brand" href="index.php">WHS CRM - Panel Admina</a>
        <span class="navbar-text text-white">Administrator: <?= htmlspecialchars($_SESSION['user']['nazwisko']) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Wyloguj</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card stat-card bg-primary text-white shadow">
                <div class="card-body text-center">
                    <h3><?= $total_tickets ?></h3>
                    <p class="mb-0">Wszystkich zgłoszeń</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card stat-card bg-success text-white shadow">
                <div class="card-body text-center">
                    <h3><?= $total_replies ?></h3>
                    <p class="mb-0">Wszystkich odpowiedzi</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-white"><h5>Ranking Pracowników</h5></div>
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Pracownik</th>
                        <th>Obsłużone zgłoszenia</th>
                        <th>Liczba wiadomości</th>
                        <th>Średnia ocena</th>
                        <th>Tempo pracy (relatywne)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($worker_stats as $ws): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($ws['nazwisko']) ?></strong></td>
                            <td><?= $ws['tickets_handled'] ?></td>
                            <td><?= $ws['replies_count'] ?></td>
                            <td>
                                <?= $ws['avg_rating'] ? number_format($ws['avg_rating'], 1) . ' ' . str_repeat('⭐', round($ws['avg_rating'])) : '<span class="text-muted">Brak ocen</span>' ?>
                            </td>
                            <td>
                                <span class="icon-tempo" title="Relacja do średniej: <?= $avg_tickets_handled > 0 ? round($ws['tickets_handled'] / $avg_tickets_handled * 100) : 0 ?>%">
                                    <?= getTempoIcon($ws['tickets_handled'], $avg_tickets_handled) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white"><h5>Popularność Zagadnień</h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach($topic_stats as $ts): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= $ts['nazwa'] ?>
                                <span class="badge bg-secondary rounded-pill"><?= $ts['total'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
             <div class="card shadow">
                <div class="card-header bg-white"><h5>Logi ostatnich logowań</h5></div>
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0" style="font-size: 0.8rem;">
                        <thead>
                            <tr>
                                <th>Użytkownik</th>
                                <th>IP</th>
                                <th>System</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $logi = $pdo->query("SELECT l.*, u.nazwisko FROM logi l JOIN uzytkownicy u ON l.id_uzytkownika = u.id ORDER BY l.data_godzina DESC LIMIT 10")->fetchAll();
                            foreach($logi as $l): ?>
                                <tr>
                                    <td><?= htmlspecialchars($l['nazwisko']) ?></td>
                                    <td><?= $l['ip_address'] ?></td>
                                    <td><?= $l['system_operacyjny'] ?></td>
                                    <td><?= $l['data_godzina'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 text-center text-muted pb-4">
    <p>&copy; 2026 WHS CRM - Panel Administracyjny</p>
</footer>
</body>
</html>
