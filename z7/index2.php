<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Klient SSE i format JSON</title>
</head>
<body>
<h3>Ostatni wpis w bazie (SSE Live):</h3>
<div id="result" style="font-size: 24px; border: 2px solid green; padding: 10px;">
    Czekam na dane...
</div>
<h3>Odczyt JSON (SSE)</h3>
<div class="alert alert-success">
    <strong>Ostatni odczyt:</strong> <span id="json-raw">...</span>
</div>

X1: <div class="progress mb-2"><div id="bar1" class="progress-bar" style="width: 0%"></div></div>
X2: <div class="progress mb-2"><div id="bar2" class="progress-bar bg-success" style="width: 0%"></div></div>
X3: <div class="progress mb-2"><div id="bar3" class="progress-bar bg-info" style="width: 0%"></div></div>

<script>
    var source = new EventSource("json_from_db.php");

    source.onmessage = function(event) {
        // Parsujemy JSON z serwera na obiekt JS
        var obj = JSON.parse(event.data);

        // Wyświetlamy surowy JSON
        document.getElementById("json-raw").innerText = event.data;

        // Aktualizujemy paski (zakładając, że wartości są 0-100)
        document.getElementById("bar1").style.width = obj.x1 + "%";
        document.getElementById("bar1").innerText = obj.x1;

        document.getElementById("bar2").style.width = obj.x2 + "%";
        document.getElementById("bar2").innerText = obj.x2;

        document.getElementById("bar3").style.width = obj.x3 + "%";
        document.getElementById("bar3").innerText = obj.x3;
    };
</script>
<script>
    // Tworzymy połączenie SSE z plikiem PHP
    if(typeof(EventSource) !== "undefined") {
        var source = new EventSource("text_from_db.php");

        // Gdy przyjdzie wiadomość
        source.onmessage = function(event) {
            document.getElementById("result").innerHTML = event.data;
        };

        // Obsługa błędów (opcjonalna)
        source.onerror = function() {
            console.log("Błąd połączenia SSE.");
        };
    } else {
        document.getElementById("result").innerHTML = "Twoja przeglądarka nie obsługuje SSE.";
    }
</script>
</body>
</html>