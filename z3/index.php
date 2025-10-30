<?php
declare(strict_types=1);
// Uruchamiamy sesję, bo będziemy z niej korzystać
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Damian Skonieczny - z3</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">

</head>

<body>

<?php require_once 'header.php'; ?>

<main>
    <section class="sekcja1">
        <div class="container-fluid">
            <?php
            // --- Nasz router PHP dla zadania 3 ---
            $page = $_GET['page'] ?? 'home'; // Domyślnie ładuj 'home'

            // Definiujemy pliki, które można wczytać
            $allowed_pages = [
                'home' => 'pages/home.php',
                'testy_serwera' => 'pages/testy_serwera.php', // Zadania 4-9
                'geolokalizacja_ip' => 'pages/geolokalizacja_ip.php', // Zadanie 11
                'log_wizyt' => 'pages/log_wizyt.php' // Zadanie 12-16
            ];

            if (array_key_exists($page, $allowed_pages)) {
                $file_to_include = $allowed_pages[$page];
                if (file_exists($file_to_include)) {
                    include $file_to_include;
                } else {
                    echo '<h3>Błąd 404</h3><p>Nie znaleziono pliku: ' . htmlspecialchars($file_to_include) . '</p>';
                }
            } else {
                echo '<h3>Błąd 404</h3><p>Strona nie została znaleziona.</p>';
                include 'pages/home.php';
            }
            ?>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>