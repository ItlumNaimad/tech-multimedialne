<?php
require_once 'database.php';

// Nagłówki wymagane dla SSE
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

// Pętla nasłuchująca
while (!connection_aborted()) {
    // Pobieramy OSTATNI pomiar
    $sql = "SELECT * FROM pomiary ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($polaczenie, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        // Składamy format CSV: x1 [tab] x2 [tab] ...
        $data = $row['x1'] . "\t" . $row['x2'] . "\t" . $row['x3'] . "\t" . $row['x4'] . "\t" . $row['x5'];

        // Wysyłamy zdarzenie "message"
        echo "data: " . $data . "\n\n";
    }

    // WYMUSZENIE WYSŁANIA DANYCH (obejście buforowania PHP)
    while (ob_get_level() > 0) { ob_end_flush(); }
    flush();

    // Czekamy 1s
    sleep(1);
}
?>