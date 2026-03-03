<?php
/**
 * Plik: stats.php
 * Cel: Moduł prezentacji danych analitycznych.
 * Funkcjonalność: Wyświetla historię odwiedzin zarejestrowaną przez tracker.js.
 * Wykorzystane biblioteki: Bootstrap Icons.
 * Sposób działania: Pobiera 50 ostatnich wpisów z tabeli 'visitor_logs' i renderuje je w formie tabeli z ikonami przeglądarek oraz linkami do map Google (jeśli dostępne są współrzędne GPS).
 */
session_start();
require_once 'db_connect.php';

// Ochrona sesji
if (!isset($_SESSION['loggedin']) || ($_SESSION['app_id'] ?? '') !== 'lab12') {
    header("Location: index.php?page=logowanie");
    exit();
}

// Pobieranie logów
$sql = "SELECT * FROM visitor_logs ORDER BY recorded DESC LIMIT 50";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Statystyki Odwiedzin - SCADA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<?php include 'header.php'; ?>

<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-people-fill text-primary"></i> Ostatnie Odwiedziny (Analityka Tracker.js)</h5>
            <a href="scada.php" class="btn btn-outline-secondary btn-sm">Powrót do Panelu</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Lokalizacja (Lat, Lon)</th>
                            <th>Rozdzielczość</th>
                            <th>Przeglądarka</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="small text-nowrap"><?php echo $row['recorded']; ?></td>
                                    <td>
                                        <?php if ($row['latitude']): ?>
                                            <a href="https://www.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>" target="_blank" class="badge bg-info text-decoration-none">
                                                <i class="bi bi-geo-alt"></i> <?php echo round($row['latitude'], 4); ?>, <?php echo round($row['longitude'], 4); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Brak GPS</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['resolution']); ?></td>
                                    <td class="small text-muted" title="<?php echo htmlspecialchars($row['browser_info']); ?>">
                                        <?php 
                                            $ua = $row['browser_info'];
                                            if (strpos($ua, 'Firefox') !== false) echo '<i class="bi bi-browser-firefox"></i> Firefox';
                                            elseif (strpos($ua, 'Chrome') !== false) echo '<i class="bi bi-browser-chrome"></i> Chrome';
                                            elseif (strpos($ua, 'Safari') !== false) echo '<i class="bi bi-browser-safari"></i> Safari';
                                            elseif (strpos($ua, 'Edge') !== false) echo '<i class="bi bi-browser-edge"></i> Edge';
                                            else echo 'Inna';
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-4">Brak danych w bazie.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>