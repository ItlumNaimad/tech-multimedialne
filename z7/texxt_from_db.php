<?php
// Dołączamy plik bazy danych
require_once 'database.php';

// Nagłówek informujący przeglądarkę, że to strumień zdarzeń
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");

// Pętla nieskończona (działa dopóki klient nie zerwie połączenia)
while (!connection_aborted()) {

    // Pobieramy OSTATNI rekord z tabeli
    $query = "SELECT * FROM ajax_from_db ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($polaczenie, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        $text = $row['text1'];

        // Format danych SSE: "data: TREŚĆ\n\n"
        echo "data: " . $text . "\n\n";
    }

    // Wypchnięcie bufora do przeglądarki (kluczowe!)
    ob_flush();
    flush();

    // Czekamy 1 sekundę przed kolejnym sprawdzeniem
    sleep(1);
}
?>

