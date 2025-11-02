<h3>Zadanie 12-17: Logowanie i Wyświetlanie Wizyt Gości</h3>
<p>Ta strona wyświetla listę gości pogrupowaną według unikalnego adresu IP i posortowaną malejąco według liczby wizyt (Zad. 17b/c).</p>

<?php
// Dołączamy nasze połączenie PDO
require_once '../src/database.php';
// Dołączamy naszą funkcję geolokalizacji
require_once '../src/utils.php';
?>

<hr>
<h4>Zarejestrowane wizyty gości (pogrupowane wg IP):</h4>
<div class="table-responsive">
    <table class="table table-striped table-hover table-sm">
        <thead class="table-dark">
        <tr>
            <th>L.p.</th>
            <th>Adres IP</th>
            <th>Ilość Wizyt (Zad. 17b)</th>
            <th>Ostatnia Wizyta</th>
            <th>Przeglądarka (przykład)</th>
            <th>Miasto/Region</th>
            <th>Mapa</th>
            <th>Język</th>
        </tr>
        </thead>
        <tbody>
        <?php
        try {
            // --- ZMODYFIKOWANE ZAPYTANIE (Zad. 17b/c) ---
            // Grupujemy po IP, liczymy wizyty (COUNT), sortujemy malejąco (DESC)
            // Bierzemy też ostatnią datę (MAX) i jeden z przykładów przeglądarki/języka
            $sql_select = "
                        SELECT 
                            ipaddress, 
                            COUNT(*) as visit_count,
                            MAX(datetime) as last_visit,
                            MAX(browser) as browser,
                            MAX(language) as language
                        FROM 
                            goscieportalu 
                        GROUP BY 
                            ipaddress
                        ORDER BY 
                            visit_count DESC
                    ";
            $stmt_select = $pdo->query($sql_select);

            $lp = 1; // Licznik porządkowy

            while ($row = $stmt_select->fetch()) {

                // Pobieramy dane geo dla IP (bez zmian)
                $details = ip_details($row['ipaddress']);
                $city = $details->city ?? 'Brak';
                $region = $details->region ?? 'danych';
                $loc = $details->loc ?? '';

                echo '<tr>';
                echo '<td>' . $lp++ . '</td>';
                echo '<td>' . htmlspecialchars($row['ipaddress']) . '</td>';
                // Nowa kolumna z liczbą wizyt
                echo '<td><b>' . htmlspecialchars($row['visit_count']) . '</b></td>';
                echo '<td>' . htmlspecialchars($row['last_visit']) . '</td>';
                echo '<td title="' . htmlspecialchars($row['browser']) . '">' . htmlspecialchars(substr($row['browser'], 0, 30)) . '...</td>';
                echo '<td>' . htmlspecialchars($city) . ' / ' . htmlspecialchars($region) . '</td>';

                if (!empty($loc)) {
                    echo '<td><a href="https://www.google.pl/maps/place/' . htmlspecialchars($loc) . '" target="_blank" class="btn btn-sm btn-info">LINK</a></td>';
                } else {
                    echo '<td>-</td>';
                }

                echo '<td>' . htmlspecialchars($row['language']) . '</td>';
                echo '</tr>';
            }

        } catch (PDOException $e) {
            echo '<tr><td colspan="8" class="text-danger">Błąd podczas pobierania wizyt: ' . $e->getMessage() . '</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>