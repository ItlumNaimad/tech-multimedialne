<?php
declare(strict_types=1);
session_start();

// --- NOWA LOGIKA: PRZEKIEROWANIE NIEZALOGOWANYCH ---
$page = $_GET['page'] ?? 'home';

// Sprawdzamy, czy użytkownik jest niezalogowany
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {

    // Jeśli jest niezalogowany ORAZ NIE próbuje wejść na 'logowanie' lub 'rejestracja'
    if ($page !== 'logowanie' && $page !== 'rejestracja') {

        // Wymuś przekierowanie do strony logowania w z4
        header('Location: index.php?page=logowanie');
        exit(); // Zawsze kończ skrypt po przekierowaniu
    }
}
// --- KONIEC NOWEJ LOGIKI ---
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Twój Opis">
    <meta name="author" content="Damian Skonieczny">
    <meta name="keywords" content="Technologie Multimedialne, Bootstrap">
    <title>Damian Skonieczny - z2</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">
    <!-- ikony Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>
    <?php if ($page === 'logowanie' || $page === 'rejestracja'): ?>
        <style>
            html, body {
                height: 100%;
            }
            body {
                display: flex;
                align-items: center;
                padding-top: 40px;
                padding-bottom: 40px;
                background-color: #f5f5f5;
            }
            .sekcja1 {
                width: 100%;
            }
        </style>
    <?php endif; ?>
</head>
<body>
<?php
// --- NOWA LOGIKA: POKAŻ HEADER TYLKO GDY TRZEBA ---
// Nie pokazuj paska nawigacyjnego na stronie logowania i rejestracji
if ($page !== 'logowanie' && $page !== 'rejestracja'):
    ?>
    <?php require_once 'header.php'; ?>
<?php endif; ?>

<main>
    <section class="sekcja1">
        <div class="container-fluid">
            <?php
            // --- Krok 2: Ulepszony router PHP ---

            // Sprawdzamy, czy w adresie URL jest parametr "page"
            $page = $_GET['page'] ?? 'home'; // Domyślnie ładuj 'home'

            // Bezpieczniej jest zdefiniować, jakie pliki można wczytać
            $allowed_pages = [
                    'home' => 'strona_glowna.php', // Zrobimy ten plik za chwilę
                    'p1_1' => 'polecenie1_1.php',
                    'p1_2' => 'polecenie1_2.php',
                    'p2_1' => 'polecenie2_1.php',
                    'p2_2' => 'polecenie2_2.php',
                    'p3_1' => 'polecenie3_1.php',
                    'p3_2' => 'polecenie3_2.php',
                    'p3_3' => 'polecenie3_3.php',
                    'logowanie' => 'pages/logowanie.php',
                    'rejestracja' => 'pages/rejestracja.php',
                    'panel' => 'pages/panel.php',
                    'wyloguj' => 'wyloguj.php'
            ];

            // Sprawdzamy, czy żądana strona jest na naszej liście
            if (array_key_exists($page, $allowed_pages)) {
                $file_to_include = $allowed_pages[$page];

                // Sprawdzamy, czy ten plik fizycznie istnieje
                if (file_exists($file_to_include)) {
                    include $file_to_include;
                } else {
                    echo '<h3>Błąd 404</h3><p>Nie znaleziono pliku: ' . htmlspecialchars($file_to_include) . '</p>';
                }
            } else {
                // Jeśli ktoś wpisze zły ?page=...
                echo '<h3>Błąd 404</h3><p>Strona nie została znaleziona.</p>';
                include 'strona_glowna.php';
            }
            ?>
        </div>
    </section>
</main>
<?php
// --- NOWA LOGIKA: POKAŻ FOOTER TYLKO GDY TRZEBA ---
if ($page !== 'logowanie' && $page !== 'rejestracja'):
    ?>
    <?php require_once 'footer.php'; ?>
<?php endif; ?>


</body>
</html>