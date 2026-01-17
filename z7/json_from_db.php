<?php
require_once 'database.php';

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

while (!connection_aborted()) {
    $sql = "SELECT x1, x2, x3, x4, x5 FROM pomiary ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($polaczenie, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        // PHP automatycznie zamienia tablicę na JSON string
        $jsonData = json_encode($row);
        echo "data: " . $jsonData . "\n\n";
    }

    while (ob_get_level() > 0) { ob_end_flush(); }
    flush();
    sleep(1);
}
?>