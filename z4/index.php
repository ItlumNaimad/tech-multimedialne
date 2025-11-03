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
    <title>Damian Skonieczny - z4</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="twoj_css.css">

    <?php if ($page === 'logowanie' || $page === 'rejestracja'): ?>
        <style>
            html, body {
                height: 100%;
            }
            body {
                display: flex;
                align-items: center; /* Centruje w pionie */
                justify-content: center; /* DODAJ TO (Centruje w poziomie) */
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
            // Router dla z4 (lekko uproszczony)
            $allowed_pages = [
                    'home' => 'pages/monitor.php',
                    'logowanie' => 'pages/logowanie.php',
                    'rejestracja' => 'pages/rejestracja.php',
                    'dodaj' => 'pages/dodaj.php'
            ];

            // Sprawdzamy, czy strona jest dozwolona i czy plik istnieje
            if (array_key_exists($page, $allowed_pages) && file_exists($allowed_pages[$page])) {
                include $allowed_pages[$page];
            } else {
                // Domyślna strona (dla zalogowanych to monitor, dla reszty logowanie)
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                    include 'pages/monitor.php';
                } else {
                    include 'pages/logowanie.php';
                }
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
</body>
</html>