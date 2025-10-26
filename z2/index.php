<?php
declare(strict_types=1);  /* Ta linia musi być pierwsza */
session_start(); // Uruchamiamy sesję OD RAZU, będziemy jej potrzebować
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap5.min.js"></script>

</head>

<body>

<?php require_once 'header.php'; ?>

<main>
    <section class="sekcja1">
        <div class="container-fluid">
            <?php
            // --- Krok 2: Prosty router PHP ---

            // Sprawdzamy, czy w adresie URL jest parametr "page"
            $page = $_GET['page'] ?? 'home'; // Domyślnie ładuj 'home'

            // Logika wczytywania odpowiedniej treści
            switch ($page) {
                case 'p1_1':
                    echo '<h3>Polecenie 1.1</h3>';
                    // Tutaj w przyszłości wstawimy treść tego zadania
                    break;
                case 'p1_2':
                    echo '<h3>Polecenie 1.2</h3>';
                    break;
                case 'p2_1':
                    echo '<h3>Polecenie 2.1</h3>';
                    break;
                case 'p2_2':
                    echo '<h3>Polecenie 2.2</h3>';
                    break;
                case 'p3_1':
                    echo '<h3>Polecenie 3.1</h3>';
                    break;
                case 'p3_2':
                    echo '<h3>Polecenie 3.2</h3>';
                    break;
                case 'home':
                default:
                    echo '<h3>Strona główna</h3>';
                    echo '<p>Witaj na stronie laboratorium 2. Wybierz polecenie z menu powyżej.</p>';
                    break;
            }
            ?>
        </div>
    </section>
</main>

<?php require_once 'footer.php'; ?>

</body>
</html>