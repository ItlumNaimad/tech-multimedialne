<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $x0 = (int)$_POST['x0'];
    $y0 = (int)$_POST['y0'];
    $x_delta = (int)$_POST['x_delta'];
    $y_delta = (int)$_POST['y_delta'];
    $begin = (float)$_POST['begin'];
    $diameter = (int)$_POST['diameter'];
    $time = (float)$_POST['time'];
    $color = mysqli_real_escape_string($conn, $_POST['color']);

    $sql = "INSERT INTO animacje (x0, y0, x_delta, y_delta, `begin`, diameter, `time`, color) 
            VALUES ($x0, $y0, $x_delta, $y_delta, $begin, $diameter, $time, '$color')";

    if (mysqli_query($conn, $sql)) {
        header("Location: scada.php");
        exit();
    } else {
        echo "Błąd: " . mysqli_error($conn);
    }
} else {
    header("Location: formularz.php");
    exit();
}
?>