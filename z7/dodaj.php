<?php
// Włączamy raportowanie błędów (Diagnostyka błędu 500)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sprawdzamy czy plik bazy istnieje
if (!file_exists('database.php')) {
    die("Błąd: Nie znaleziono pliku database.php w folderze " . __DIR__);
}

require_once 'database.php';

// Sprawdzamy czy połączenie zostało utworzone (czy zmienna z database.php istnieje)
if (!isset($polaczenie)) {
    die("Błąd krytyczny: Zmienna \$polaczenie nie istnieje. Sprawdź czy plik database.php używa mysqli_connect!");
}

if (!empty($_POST['text1'])) {
    // Teraz to powinno zadziałać, jeśli $polaczenie jest poprawne
    $text = mysqli_real_escape_string($polaczenie, $_POST['text1']);

    $sql = "INSERT INTO ajax_from_db (text1) VALUES ('$text')";

    if (mysqli_query($polaczenie, $sql)) {
        echo "Dodano: $text. Możesz zamknąć to okno.";
    } else {
        echo "Błąd SQL: " . mysqli_error($polaczenie);
    }
} else {
    echo "Błąd: Puste pole text1.";
}
?>