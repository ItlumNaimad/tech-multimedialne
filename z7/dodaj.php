<?php
require_once 'database.php';

if (!empty($_POST['text1'])) {
    $text = mysqli_real_escape_string($polaczenie, $_POST['text1']);

    $sql = "INSERT INTO ajax_from_db (text1) VALUES ('$text')";

    if (mysqli_query($polaczenie, $sql)) {
        echo "Dodano: $text. Możesz zamknąć to okno.";
    } else {
        echo "Błąd: " . mysqli_error($polaczenie);
    }
}
?>