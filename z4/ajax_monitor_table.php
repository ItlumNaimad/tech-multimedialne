<?php
/*
    Ten plik zwraca teraz obiekt JSON:
    {
        "html": "...", // Kod HTML tabeli
        "alarm": true/false // Sygnał do włączenia alarmu
    }
*/
session_start();
require_once '../src/database.php'; // Łączymy się z bazą

// Sprawdzamy sesję
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['html' => '<div class="alert alert-danger">Błąd sesji. Zaloguj się ponownie.</div>', 'alarm' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// --- NOWA LOGIKA: Buforowanie wyjścia ---
// Zamiast od razu wysyłać HTML (przez echo), zbieramy go do zmiennej $html_content.
ob_start();

// Ustawiamy flagę alarmu
$alarm_triggered = false;
?>
    <div class="table-responsive">
        <table class="table table-striped table-hover table-sm">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Host</th>
                <th>Port</th>
                <th>Status (Pkt 8)</th>
                <th>Akcje (Pkt 14)</th>
            </tr>
            </thead>
            <tbody>
            <?php
            try {
                // Logika pobierania hostów (bez zmian)
                if ($username === 'admin') {
                    $sql_select = "SELECT * FROM domeny";
                    $stmt_select = $pdo->prepare($sql_select);
                    $stmt_select->execute();
                } else {
                    $sql_select = "SELECT * FROM domeny WHERE user_id = :user_id";
                    $stmt_select = $pdo->prepare($sql_select);
                    $stmt_select->execute(['user_id' => $user_id]);
                }

                while ($row = $stmt_select->fetch()) {

                    // Logika sprawdzania portu
                    $timeout = 2;
                    $fp = @stream_socket_client("tcp://{$row['host']}:{$row['port']}", $errno, $errstr, $timeout);

                    if ($fp) {
                        $stan = '<span class="badge bg-success">OK</span>';
                        fclose($fp);
                    } else {
                        $stan = '<span class="badge bg-danger" title="' . htmlspecialchars("$errno: $errstr") . '">Awaria</span>';
                        $alarm_triggered = true; // <-- USTAWIAMY FLAGĘ ALARMU!
                    }

                    // Wypisujemy wiersz tabeli (bez zmian)
                    echo '<tr>';
                    echo '<td>' . $row['id'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['host']) . '</td>';
                    echo '<td>' . htmlspecialchars((string)$row['port']) . '</td>';
                    echo '<td>' . $stan . '</td>';
                    if ($username === 'admin' || $row['user_id'] == $user_id) {
                        echo '<td>
                                <a href="usun_handler.php?id=' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Czy na pewno chcesz usunąć ten host?\');">
                                    <i class="bi bi-trash"></i> Usuń
                                </a>
                              </td>';
                    } else {
                        echo '<td>(Tylko admin)</td>';
                    }
                    echo '</tr>';
                }

            } catch (PDOException $e) {
                echo '<tr><td colspan="5" class="text-danger">Błąd bazy danych: ' . $e.getMessage() . '</td></tr>';
                $alarm_triggered = true; // Błąd bazy to też alarm
            }
            ?>
            </tbody>
        </table>
    </div>

<?php
// --- Zakończenie buforowania i wysłanie JSON ---

// 1. Pobieramy cały wygenerowany HTML do zmiennej
$html_content = ob_get_clean();

// 2. Ustawiamy nagłówek na JSON
header('Content-Type: application/json');

// 3. Wysyłamy obiekt JSON
echo json_encode([
        'html' => $html_content,
        'alarm' => $alarm_triggered
]);
?>