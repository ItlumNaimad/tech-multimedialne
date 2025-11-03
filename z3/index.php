<?php
declare(strict_types=1);
session_start();

// --- UPROSZCZONA LOGIKA PRZEKIEROWANIA DLA Z3 ---
// Ta strona jest TYLKO dla zalogowanych. Jeśli nie ma sesji, przekieruj do logowania w z2.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Używamy ścieżki absolutnej (zaczynającej się od /), aby na pewno trafić do z2
    header('Location: /z2/index.php?page=logowanie');
    exit(); // Zawsze kończ skrypt po przekierowaniu
}

// Jeśli doszliśmy tutaj, użytkownik JEST zalogowany.
$page = $_GET['page'] ?? 'home';
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
            // Router dla z3 (już bez stron logowania/rejestracji)
            $allowed_pages = [
                    'home' => 'pages/home.php',
                    'testy_serwera' => 'pages/testy_serwera.php',
                    'geolokalizacja_ip' => 'pages/geolokalizacja_ip.php',
                    'log_wizyt' => 'pages/log_wizyt.php'
            ];

            if (array_key_exists($page, $allowed_pages) && file_exists($allowed_pages[$page])) {
                include $allowed_pages[$page];
            } else {
                // Domyślną stroną jest 'home' (czyli 'pages/home.php')
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