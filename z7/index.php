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
                    Generuj Dane (Otwórz w nowym oknie)
                </a>
                <a href="formularz.php" class="list-group-item list-group-item-action">
                    Stary Formularz (Tekst)
                </a>
            </div>
        </div>
        <div class="col-md-6">
            <h4>Wizualizacje SSE</h4>
            <div class="list-group">
                <a href="index1.php" class="list-group-item list-group-item-action">1. Format CSV (Tabulacja)</a>
                <a href="index2.php" class="list-group-item list-group-item-action">2. Format JSON (Wykresy)</a>
                <a href="index3.php" class="list-group-item list-group-item-action">3. Format XML (Parsowanie)</a>
            </div>
        </div>
    </div>
    
    <div class="alert alert-info mt-4">
        Jeśli dane się nie aktualizują:
        <ol>
            <li>Otwórz <b>Generuj Dane</b>.</li>
            <li>Otwórz np. <b>Format JSON</b>.</li>
            <li>Wyślij formularz i patrz czy JSON się zmienia.</li>
            <li>Jeśli nadal wisi na "Czekam...", to wina buforowania hostingu (kod jest poprawny).</li>
        </ol>
    </div>
</body>
</html>