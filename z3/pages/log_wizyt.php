<h3>Zadanie 12-14: Logowanie i Wyświetlanie Wizyt Gości</h3>
<p>Ta strona automatycznie zapisuje Twoją wizytę w bazie danych, a następnie wyświetla pełną listę wszystkich gości.</p>

<?php
// Dołączamy nasze połączenie PDO z folderu src (../src)
require_once '../src/database.php';

// --- ZADANIE 12: ZAPISYWANIE WIZYTY ---

// Pobieramy adres IP gościa
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
}

try {
    // Przygotuj zapytanie INSERT, aby dodać gościa do tabeli
    $sql_insert = "INSERT INTO goscieportalu (ipaddress) VALUES (:ip)";
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute(['ip' => $ipaddress]);

    // Wiadomość o sukcesie (opcjonalnie)
    echo '<div class="alert alert-success">Twoja wizyta (IP: ' . htmlspecialchars($ipaddress) . ') została pomyślnie zarejestrowana.</div>';

} catch (PDOException $e) {
    echo '<div class="alert alert-danger">Błąd podczas zapisywania wizyty: ' . $e->getMessage() . '</div>';
}

// --- FUNKCJA Z ZADANIA 11 ---
// Potrzebujemy jej ponownie, aby pobrać dane dla każdego IP z listy
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
    <table class="table table-striped table-hover">
        <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Data i Godzina</th>
            <th>Adres IP</th>
            <th>Miasto</th>
            <th>Region</th>
            <th>Współrzędne</th>
            <th>Mapa (Zad. 13)</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // --- ZADANIE 12 i 13: WYŚWIETLANIE WIZYT ---
        try {
            // 1. Pobierz wszystkie rekordy z tabeli goscieportalu
            $sql_select = "SELECT id, ipaddress, datetime FROM goscieportalu ORDER BY datetime DESC";
            $stmt_select = $pdo->query($sql_select);

            // 2. Przejdź przez każdy rekord (wiersz)
            while ($row = $stmt_select->fetch()) {

                // 3. Pobierz dane geo dla danego IP
                $details = ip_details($row['ipaddress']);

                // Ustaw puste wartości, jeśli ipinfo.io zawiedzie
                $city = $details->city ?? 'Brak danych';
                $region = $details->region ?? 'Brak danych';
                $loc = $details->loc ?? '';

                // 4. Wyświetl wiersz tabeli
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['datetime']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ipaddress']) . '</td>';
                echo '<td>' . htmlspecialchars($city) . '</td>';
                echo '<td>' . htmlspecialchars($region) . '</td>';
                echo '<td>' . htmlspecialchars($loc) . '</td>';

                // 5. ZADANIE 13: Link do Google Maps [cite: 471-472]
                if (!empty($loc)) {
                    echo '<td><a href="https://www.google.pl/maps/place/' . htmlspecialchars($loc) . '" target="_blank" class="btn btn-sm btn-info">LINK</a></td>';
                } else {
                    echo '<td>-</td>';
                }
                echo '</tr>';
            }

        } catch (PDOException $e) {
            echo '<tr><td colspan="7" class="text-danger">Błąd podczas pobierania wizyt: ' . $e->getMessage() . '</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>