<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Zadanie AJAX + SSE</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .measurement-box { border: 2px solid #0d6efd; padding: 10px; margin: 5px; text-align: center; font-weight: bold; }
    </style>
</head>
<body class="container mt-4">
    <!-- Sekcja JSON -->
    <h3>Dane z pliku JSON (odświeżane co 2s):</h3>
    <p>Imię: <span class="status" style="font-weight:bold; color:blue;">...</span></p>

    <hr>

    <!-- Sekcja CSV (SSE) -->
    <h3>Odczyt CSV (SSE)</h3>
    <div class="row">
        <div class="col-2"><div class="measurement-box" id="x1">--</div><small>Czujnik 1</small></div>
        <div class="col-2"><div class="measurement-box" id="x2">--</div><small>Czujnik 2</small></div>
        <div class="col-2"><div class="measurement-box" id="x3">--</div><small>Czujnik 3</small></div>
        <div class="col-2"><div class="measurement-box" id="x4">--</div><small>Czujnik 4</small></div>
        <div class="col-2"><div class="measurement-box" id="x5">--</div><small>Czujnik 5</small></div>
    </div>

    <!-- Skrypt JSON Polling -->
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

    <!-- Skrypt SSE CSV -->
    <script>
        if(typeof(EventSource) !== "undefined") {
            var source = new EventSource("csv_from_db.php");
            
            source.onmessage = function(event) {
                // Rozdzielamy string po znaku tabulacji (\t) - zgodnie z instrukcją
                var data = event.data.split("\t");
                
                if(data.length >= 5) {
                    document.getElementById("x1").innerText = data[0];
                    document.getElementById("x2").innerText = data[1];
                    document.getElementById("x3").innerText = data[2];
                    document.getElementById("x4").innerText = data[3];
                    document.getElementById("x5").innerText = data[4];
                }
            };
        } else {
            document.body.innerHTML += "<br>Twoja przeglądarka nie obsługuje SSE.";
        }
    </script>
</body>
</html>