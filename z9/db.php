<?php
mysqli_report(MYSQLI_REPORT_OFF);

$host = 'localhost'; 
$user = 'root'; 
$pass = ''; 
$dbname = 'tech_multimedialne'; 

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Połączenie nieudane.");
}

mysqli_set_charset($conn, "utf8");
?>