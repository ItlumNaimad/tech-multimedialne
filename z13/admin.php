<?php
/**
 * Plik: admin.php
 * Cel: Panel administratora z rozbudowaną analityką wydajności pracowników.
 */
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['username'] !== 'admin') {
    header("Location: index.php");
    exit();
}

try {
    // 1. Historia logowań
    $stmt_logs = $pdo->query("SELECT l.*, p.login FROM logowanie l LEFT JOIN pracownik p ON l.idp = p.idp ORDER BY l.datetime DESC");
    $logs = $stmt_logs->fetchAll();

    // 2. Wszystkie zadania i podzadania
    $stmt_tasks = $pdo->query("SELECT z.nazwa_zadania, pm.login AS manager, pz.nazwa_podzadania, w.login AS worker, pz.stan 
                                FROM zadanie z
                                JOIN pracownik pm ON z.idp = pm.idp
                                LEFT JOIN podzadanie pz ON z.idz = pz.idz
                                LEFT JOIN pracownik w ON pz.idp = w.idp
                                ORDER BY z.idz, pz.idpz");
    $tasks = $stmt_tasks->fetchAll();

    // 3. Statystyki pracowników (punkt 10c, 10f)
    $stmt_stats = $pdo->query("SELECT p.idp, p.login, AVG(pz.stan) as avg_stan 
                                FROM pracownik p 
                                LEFT JOIN podzadanie pz ON p.idp = pz.idp 
                                GROUP BY p.idp");
    $worker_stats = $stmt_stats->fetchAll();

    // Obliczanie średniej zespołu
    $team_avg = 0;
    $valid_workers = 0;
    $max_avg = -1;
    $min_avg = 101;

    foreach ($worker_stats as $ws) {
        if ($ws['avg_stan'] !== null) {
            $team_avg += $ws['avg_stan'];
            $valid_workers++;
            if ($ws['avg_stan'] > $max_avg) $max_avg = $ws['avg_stan'];
            if ($ws['avg_stan'] < $min_avg) $min_avg = $ws['avg_stan'];
        }
    }
    if ($valid_workers > 0) $team_avg /= $valid_workers;

} catch (PDOException $e) {
    die("Błąd bazy danych: " . $e->getMessage());
}

function get_worker_symbol($avg, $team_avg, $min, $max) {
    if ($avg === null) return "❓";
    if ($avg == $max && $avg > $team_avg) return "🐆"; // Puma - najszybszy
    if ($avg == $min && $avg < $team_avg) return "🐌"; // Ślimak - najwolniejszy
    if ($avg < $team_avg) return "🐢"; // Żółw - powolny
    return "🚶"; // Człowiek - przeciętny/dobry
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Admin - ToDo Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .symbol { font-size: 1.5rem; }
        .card-stat { border-left: 5px solid #0d6efd; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Panel Admina - Analityka</span>
        <div class="ms-auto">
            <a href="index.php" class="btn btn-outline-light btn-sm me-2">Strona Główna</a>
            <a href="database/logout_handler.php" class="btn btn-danger btn-sm">Wyloguj</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm card-stat p-3">
                <h6 class="text-muted text-uppercase small">Średnia zespołu</h6>
                <h3 class="fw-bold mb-0"><?= round($team_avg, 2) ?>%</h3>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- STATYSTYKI PRACOWNIKÓW -->
        <div class="col-12 mb-5">
            <h2 class="mb-3">Wydajność Pracowników</h2>
            <div class="table-responsive bg-white p-3 rounded shadow-sm">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Pracownik</th>
                            <th>Średnia realizacja</th>
                            <th>Status Wydajności</th>
                            <th>Symbol</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($worker_stats as $ws): 
                            $symbol = get_worker_symbol($ws['avg_stan'], $team_avg, $min_avg, $max_avg);
                        ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($ws['login']) ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-2" style="height: 10px;">
                                        <div class="progress-bar bg-primary" style="width: <?= $ws['avg_stan'] ?? 0 ?>%"></div>
                                    </div>
                                    <span><?= $ws['avg_stan'] !== null ? round($ws['avg_stan'], 1) . '%' : 'Brak zadań' ?></span>
                                </div>
                            </td>
                            <td>
                                <?php 
                                    if ($symbol == "🐆") echo '<span class="badge bg-success">LIDER (Najszybszy)</span>';
                                    elseif ($symbol == "🐌") echo '<span class="badge bg-danger">OPÓŹNIONY (Najwolniejszy)</span>';
                                    elseif ($symbol == "🐢") echo '<span class="badge bg-warning text-dark">POWOLNY</span>';
                                    elseif ($symbol == "🚶") echo '<span class="badge bg-info text-dark">PRZECIĘTNY</span>';
                                    else echo '<span class="badge bg-secondary">NIEAKTYWNY</span>';
                                ?>
                            </td>
                            <td class="symbol text-center"><?= $symbol ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- LOGOWANIA -->
        <div class="col-12 mb-5">
            <h2 class="mb-3">Logowania</h2>
            <div class="table-responsive bg-white p-3 rounded shadow-sm" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-striped">
                    <thead class="table-secondary sticky-top">
                        <tr><th>User</th><th>Data</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?= $log['login'] ?? '<i>Nieznany</i>' ?></td>
                            <td><?= $log['datetime'] ?></td>
                            <td><?= $log['state'] == 0 ? 'Sukces' : 'Błąd ('.$log['state'].')' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
