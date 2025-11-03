<?php
// Ten plik zawiera tylko logikę tabeli i jest wywoływany przez AJAX
session_start();
require_once '../src/database.php'; // Musimy połączyć się z bazą

// Sprawdzamy sesję, inaczej każdy może to wywołać
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die('<div class="alert alert-danger">Błąd sesji. Zaloguj się ponownie.</div>');
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

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
            // --- Logika Pkt 15 (Admin/User) ---
            if ($username === 'admin') {
                // Admin widzi wszystko [cite: 199]
                $sql_select = "SELECT * FROM domeny";
                $stmt_select = $pdo->prepare($sql_select);
                $stmt_select->execute();
            } else {
                // Zwykły user widzi tylko swoje
                $sql_select = "SELECT * FROM domeny WHERE user_id = :user_id";
                $stmt_select = $pdo->prepare($sql_select);
                $stmt_select->execute(['user_id' => $user_id]);
            }

            // --- Logika Pkt 8 (fsockopen) ---
            while ($row = $stmt_select->fetch()) {

                // Sprawdzamy status portu
                $fp = @fsockopen($row['host'], $row['port'], $errno, $errstr, 5); // Czas oczekiwania 5s

                if ($fp) {
                    $stan = '<span class="badge bg-success">OK</span>'; // Działa
                    fclose($fp);
                } else {
                    $stan = '<span class="badge bg-danger" title="' . htmlspecialchars("$errno: $errstr") . '">Awaria</span>'; // Nie działa [cite: 112]
                }

                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . htmlspecialchars($row['host']) . '</td>';
                echo '<td>' . htmlspecialchars((string)$row['port']) . '</td>';
                echo '<td>' . $stan . '</td>';

                // --- Logika Pkt 14 (Usuwanie) ---
                // Admin może usuwać wszystko, user tylko swoje
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
            echo '<tr><td colspan="5" class="text-danger">Błąd bazy danych: ' . $e->getMessage() . '</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>