<?php
// Sprawdzenie sesji (bez zmian)
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

<audio id="alarm-sound" src="alarm.mp3" preload="auto"></audio>

<script>
    // Globalna flaga do wyciszania
    var isMuted = false;

    // Pobieramy element audio
    var alarmSound = document.getElementById("alarm-sound");

    // Funkcja do ładowania tabeli
    function loadMonitorTable() {

        // Używamy pełnej funkcji $.ajax
        $.ajax({
            url: 'ajax_monitor_table.php', // Plik docelowy
            type: 'GET',
            dataType: 'json', // Oczekujemy odpowiedzi JSON
            success: function(data) {
                // Krok 1: Wstrzyknij HTML do kontenera
                $('#monitor-table-container').html(data.html);

                // Krok 2: Sprawdź flagę alarmu
                if (data.alarm === true && !isMuted) {
                    // Odtwórz dźwięk
                    alarmSound.play().catch(function(error) {
                        // Przeglądarki często blokują autoodtwarzanie
                        // Ten błąd pojawi się w konsoli, ale nie przerwie aplikacji
                        console.warn("Nie można automatycznie odtworzyć alarmu: ", error);
                    });
                }
            },
            error: function(xhr, status, error) {
                // Obsługa błędu, jeśli sam AJAX zawiedzie
                $('#monitor-table-container').html('<div class="alert alert-danger">Błąd krytyczny ładowania danych: ' + error + '</div>');
            }
        });
    }

    // Pierwsze uruchomienie i interwał (bez zmian)
    $(document).ready(function() {
        loadMonitorTable();
        setInterval(loadMonitorTable, 10000);

        // --- NOWA LOGIKA: Obsługa przycisku wyciszania ---
        // (Ten przycisk dodamy w header.php w następnym kroku)
        $('body').on('click', '#mute-button', function(e) {
            e.preventDefault(); // Zatrzymaj domyślną akcję linku
            isMuted = !isMuted; // Odwróć stan (true/false)

            if (isMuted) {
                // Zmień ikonę na wyciszoną
                $(this).find('i').removeClass('bi-volume-up-fill').addClass('bi-volume-mute-fill');
                $(this).attr('title', 'Włącz alarmy');
                // Natychmiast zatrzymaj dźwięk, jeśli gra
                alarmSound.pause();
                alarmSound.currentTime = 0;
            } else {
                // Zmień ikonę na włączoną
                $(this).find('i').removeClass('bi-volume-mute-fill').addClass('bi-volume-up-fill');
                $(this).attr('title', 'Wycisz alarmy');
            }
        });
    });
</script>