<?php
/**
 * Plik: pages/stats.php (Wersja zintegrowana)
 * Cel: Widok analityki gości dla routera index.php.
 * Funkcjonalność: Wyświetla tabelę z danymi z visitor_logs (IP, data, koordynaty, browser info).
 */
require_once 'db_connect.php';

// Ochrona pliku przed dostępem bezpośrednim (wymuszona sesja w index.php, tu dodatkowe zabezpieczenie)
if (!isset($_SESSION['loggedin']) || ($_SESSION['app_id'] ?? '') !== 'lab12') {
    header("Location: index.php?page=logowanie");
    exit();
}

// Pobieranie logów - limitujemy do 50 najnowszych
$sql = "SELECT * FROM visitor_logs ORDER BY recorded DESC LIMIT 50";
$result = $conn->query($sql);
?>
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people-fill text-primary"></i> Analityka Wizyt (Tracker.js)</h5>
        <a href="index.php?page=scada" class="btn btn-outline-secondary btn-sm">Panel SCADA</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Data i Godzina</th>
                        <th>Adres IP</th>
                        <th>Lokalizacja (Lat, Lon)</th>
                        <th>Rozdzielczość</th>
                        <th>Cookies</th>
                        <th>Przeglądarka</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="small text-nowrap"><?php echo $row['recorded']; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['ip_address'] ?? 'Ukryty'); ?></td>
                                <td>
                                    <?php if ($row['latitude']): ?>
                                        <a href="https://www.google.com/maps?q=<?php echo $row['latitude']; ?>,<?php echo $row['longitude']; ?>" target="_blank" class="badge bg-info text-decoration-none">
                                            <i class="bi bi-geo-alt"></i> <?php echo round($row['latitude'], 4); ?>, <?php echo round($row['longitude'], 4); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Brak GPS</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small"><?php echo htmlspecialchars($row['resolution']); ?></td>
                                <td class="text-center">
                                    <?php if($row['cookies_enabled']): ?>
                                        <i class="bi bi-check-circle-fill text-success" title="Tak"></i>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle-fill text-danger" title="Nie"></i>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
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
                        <tr><td colspan="6" class="text-center py-4 text-muted">Brak zarejestrowanych wizyt w bazie.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>