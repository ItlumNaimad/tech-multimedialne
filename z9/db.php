<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'tech_multimedialne'; // I assume a common DB name or the user will adjust

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>