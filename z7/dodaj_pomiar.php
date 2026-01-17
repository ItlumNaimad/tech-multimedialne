<?php
require_once 'database.php';

if (isset($_POST['x1'])) {
    $x1 = (float)$_POST['x1'];
    $x2 = (float)$_POST['x2'];
    $x3 = (float)$_POST['x3'];
    $x4 = (float)$_POST['x4'];
    $x5 = (float)$_POST['x5'];

    $sql = "INSERT INTO pomiary (x1, x2, x3, x4, x5) VALUES ($x1, $x2, $x3, $x4, $x5)";

    if (mysqli_query($polaczenie, $sql)) {
        echo "Dodano dane o " . date('H:i:s');
    } else {
        echo "Błąd: " . mysqli_error($polaczenie);
    }
}
?>

