<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Klient SSE</title>
</head>
<body>
<h3>Ostatni wpis w bazie (SSE Live):</h3>
<div id="result" style="font-size: 24px; border: 2px solid green; padding: 10px;">
    Czekam na dane...
</div>

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