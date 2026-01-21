<?php
require_once 'database.php';

// --- FIX DLA ZETA HOSTING (Anty-buforowanie) ---
error_reporting(0); // Wyłączamy błędy PHP, żeby nie psuły strumienia
set_time_limit(0);  // Skrypt ma działać w nieskończoność
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
@ob_end_clean();

// Nagłówki SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Specjalny nagłówek dla Nginx

// Wstępny "wypychacz" (4KB spacji)
echo ":" . str_repeat(" ", 4096) . "\n\n";
flush();

while (true) {
    if (connection_aborted()) break;

    // Pobierz OSTATNI rekord
    $sql = "SELECT x1, x2, x3, x4, x5 FROM pomiary ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($polaczenie, $sql);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Dane właściwe (CSV oddzielone tabulatorem)
        $csv = $row['x1'] . "\t" . $row['x2'] . "\t" . $row['x3'] . "\t" . $row['x4'] . "\t" . $row['x5'];

        // 1. Wyślij dane
        echo "data: " . $csv . "\n\n";

        // 2. WYPYCHACZ w pętli (Klucz do działania na Zeto)
        echo ":" . str_repeat(" ", 4096) . "\n\n";

        @ob_flush();
        flush();
    }

    // Odczekaj 1 sekundę
    sleep(1);
}
?>