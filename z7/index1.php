<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zadanie AJAX</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<h3>Dane z pliku JSON (odświeżane co 2s):</h3>
<p>Imię: <span class="status" style="font-weight:bold; color:blue;">...</span></p>

<script>
    (function() {
        var status = $('.status');

        // Funkcja pobierająca dane
        var poll = function() {
            $.ajax({
                url: 'my_file.json', // Adres pliku
                dataType: 'json',    // Oczekiwany format
                type: 'get',
                cache: false,
                success: function(data) {
                    // Gdy sukces: wpisz imię do spana
                    status.text(data.imie);
                },
                error: function() {
                    console.log('Błąd pobierania JSON!');
                }
            });
        };

        // Uruchom poll() co 2000ms (2 sekundy)
        setInterval(function() {
            poll();
        }, 2000);

        // Pierwsze wywołanie od razu
        poll();
    })();
</script>
</body>
</html>