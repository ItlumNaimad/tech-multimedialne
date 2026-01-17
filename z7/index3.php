<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SSE - Format XML</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
<h3>Odczyt XML (SSE)</h3>
<ul class="list-group" id="xml-list">
    <li class="list-group-item">Czekam na dane...</li>
</ul>

<script>
    var source = new EventSource("xml_from_db.php");

    source.onmessage = function(event) {
        // Parsowanie XML w JavaScript
        var parser = new DOMParser();
        var xmlDoc = parser.parseFromString(event.data, "text/xml");

        // Pobieranie wartości z tagów
        var x1 = xmlDoc.getElementsByTagName("x1")[0].childNodes[0].nodeValue;
        var x2 = xmlDoc.getElementsByTagName("x2")[0].childNodes[0].nodeValue;
        var x3 = xmlDoc.getElementsByTagName("x3")[0].childNodes[0].nodeValue;
        var x4 = xmlDoc.getElementsByTagName("x4")[0].childNodes[0].nodeValue;
        var x5 = xmlDoc.getElementsByTagName("x5")[0].childNodes[0].nodeValue;

        var html =  '<li class="list-group-item">X1: <b>' + x1 + '</b></li>' +
            '<li class="list-group-item">X2: <b>' + x2 + '</b></li>' +
            '<li class="list-group-item">X3: <b>' + x3 + '</b></li>' +
            '<li class="list-group-item">X4: <b>' + x4 + '</b></li>' +
            '<li class="list-group-item">X5: <b>' + x5 + '</b></li>';

        document.getElementById("xml-list").innerHTML = html;
    };
</script>
</body>
</html>