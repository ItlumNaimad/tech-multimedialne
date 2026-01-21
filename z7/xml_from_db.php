<?php
require_once 'database.php';

// Anty-bufor
error_reporting(0);
set_time_limit(0);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
@ob_end_clean();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');

echo ":" . str_repeat(" ", 4096) . "\n\n";
flush();

while (true) {
    if (connection_aborted()) break;

    $sql = "SELECT x1, x2, x3, x4, x5 FROM pomiary ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($polaczenie, $sql);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Format XML
        $xml = "<dane><x1>{$row['x1']}</x1><x2>{$row['x2']}</x2><x3>{$row['x3']}</x3><x4>{$row['x4']}</x4><x5>{$row['x5']}</x5></dane>";
        echo "data: " . $xml . "\n\n";

        // Wypychacz
        echo ":" . str_repeat(" ", 4096) . "\n\n";
        @ob_flush();
        flush();
    }
    sleep(1);
}
?>