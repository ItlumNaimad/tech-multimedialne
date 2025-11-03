<?php
// Sprawdzamy, czy user jest zalogowany, jak nie to nie ma tu czego szukać
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php?page=logowanie');
    exit();
}
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Monitor Usług</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="index.php?page=dodaj" class="btn btn-sm btn-outline-success">
            <i class="bi bi-plus-circle"></i>
            Dodaj nowy host (Pkt 13)
        </a>
    </div>
</div>

<div id="monitor-table-container">
    <p class="text-center">Ładowanie danych...</p>
</div>

<script>
    // Funkcja do ładowania tabeli
    function loadMonitorTable() {
        // Używamy jQuery (którego wymagał Bootstrap z z2), żeby było szybciej
        // Pobieramy zawartość z nowego pliku i wstawiamy do diva
        $('#monitor-table-container').load('ajax_monitor_table.php');
    }

    // Uruchom funkcję pierwszy raz od razu po załadowaniu strony
    $(document).ready(function() {
        loadMonitorTable();

        // Ustaw automatyczne odświeżanie co 10 sekund (10000 ms)
        setInterval(loadMonitorTable, 10000);
    });
</script>