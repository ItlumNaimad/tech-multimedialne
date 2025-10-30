<h3>Zadanie 12-16: Logowanie i Wyświetlanie Wizyt Gości</h3>
<p>Ta strona wyświetla pełną listę wszystkich gości. Nowe wizyty są teraz rejestrowane podczas pomyślnego logowania.</p>

<?php
// Usunęliśmy stąd kod INSERT - logowanie odbywa się teraz w login_handler.php

// Dołączamy nasze połączenie PDO
require_once '../src/database.php';

// Funkcja z Zadania 11
function ip_details($ip) {
    $json = @file_get_contents("http://ipinfo.io/{$ip}/geo");
    if ($json === FALSE) {
        return null;
    }
    return json_decode($json);
}
?>

<hr>
<h4>Zarejestrowane wizyty gości:</h4>
<div class="table-responsive">
    <table class="table table-striped table-hover table-sm">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Data</th>
            <th>IP</th>
            <th>Przeglądarka (Zad. 15)</th>
            <th>Miasto/Region</th>
            <th>Mapa</th>
            <th>Ekran (Zad. 16)</th>
            <th>Okno</th>
            <th>Kolory</th>
            <th>Ciasteczka</th>
            <th>Java</th>
            <th>Język</th>
        </tr>
        </thead>
        <tbody>
        <?php
        try {
            // 1. Pobierz wszystkie rekordy (w tym nowe kolumny)
            $sql_select = "SELECT * FROM goscieportalu ORDER BY datetime DESC";
            $stmt_select = $pdo->query($sql_select);

            while ($row = $stmt_select->fetch()) {

                $details = ip_details($row['ipaddress']);
                $city = $details->city ?? 'Brak';
                $region = $details->region ?? 'danych';
                $loc = $details->loc ?? '';

                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['datetime']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ipaddress']) . '</td>';
                // Używamy substr, aby skrócić długi ciąg User-Agent
                echo '<td title="' . htmlspecialchars($row['browser']) . '">' . htmlspecialchars(substr($row['browser'], 0, 30)) . '...</td>';
                echo '<td>' . htmlspecialchars($city) . ' / ' . htmlspecialchars($region) . '</td>';

                if (!empty($loc)) {
                    echo '<td><a href="https://www.google.pl/maps/place/' . htmlspecialchars($loc) . '" target="_blank" class="btn btn-sm btn-info">LINK</a></td>';
                } else {
                    echo '<td>-</td>';
                }

                // Wyświetlamy nowe dane z Zadania 16
                echo '<td>' . htmlspecialchars($row['screen_res']) . '</td>';
                echo '<td>' . htmlspecialchars($row['window_res']) . '</td>';
                echo '<td>' . htmlspecialchars($row['colors']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['cookies_enabled']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['java_enabled']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['language']) . '</td>';

                    echo '</tr>';
                }

        } catch (PDOException $e) {
            echo '<tr><td colspan="12" class="text-danger">Błąd podczas pobierania wizyt: ' . $e->getMessage() . '</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>