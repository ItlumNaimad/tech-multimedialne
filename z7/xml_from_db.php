<?php
require_once 'database.php';

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

while (!connection_aborted()) {
    $sql = "SELECT x1, x2, x3, x4, x5 FROM pomiary ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($polaczenie, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        // Ręcznie budujemy string XML
        // Usuwamy entery w XMLu, żeby SSE wysłało to w jednej linii 'data:'
        $xml = "<dane>";
        $xml .= "<x1>".$row['x1']."</x1>";
        $xml .= "<x2>".$row['x2']."</x2>";
        $xml .= "<x3>".$row['x3']."</x3>";
        $xml .= "<x4>".$row['x4']."</x4>";
        $xml .= "<x5>".$row['x5']."</x5>";
        $xml .= "</dane>";

        echo "data: " . $xml . "\n\n";
    }

    while (ob_get_level() > 0) { ob_end_flush(); }
    flush();
    sleep(1);
}
?>