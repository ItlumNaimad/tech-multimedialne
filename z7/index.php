<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laboratorium 7 - AJAX i SSE Komplet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
<h1>Laboratorium 7: SSE (Server-Sent Events)</h1>

<div class="row">
    <div class="col-md-6">
        <h4>Panel Sterowania</h4>
        <div class="list-group">
            <a href="formularz_pomiary.php" class="list-group-item list-group-item-warning" target="_blank">
                <b>FORMULARZ SCADA</b> (Generuj Dane)
            </a>
            <a href="formularz.php" class="list-group-item list-group-item-action">
                Stary Formularz (Tekst 7a)
            </a>
        </div>
    </div>
    <div class="col-md-6">
        <h4>Wizualizacje SSE</h4>
        <div class="list-group">
            <a href="index4.php" class="list-group-item list-group-item-primary fw-bold">
                --> NOWA SCADA (Wizualizacja) <--
            </a>
            <a href="index1.php" class="list-group-item list-group-item-action">1. Format CSV (Tabulacja)</a>
            <a href="index2.php" class="list-group-item list-group-item-action">2. Format JSON (Wykresy)</a>
            <a href="index3.php" class="list-group-item list-group-item-action">3. Format XML (Parsowanie)</a>
        </div>
    </div>
</div>

<div class="alert alert-info mt-4">
    <strong>Jak testować?</strong>
    <ol>
        <li>Kliknij <b>FORMULARZ SCADA</b> (otworzy się w nowym oknie).</li>
        <li>Kliknij <b>NOWA SCADA</b>.</li>
        <li>Wpisz wartości (np. 10, 50, 95) w formularzu i kliknij "Wyślij".</li>
        <li>Zbiorniki w oknie SCADA powinny się napełnić automatycznie.</li>
    </ol>
</div>
</body>
</html>