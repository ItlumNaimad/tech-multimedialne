<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>
<?php
$user=$_POST['user'];
$pass=$_POST['pass'];
$pass_repeat=$_POST['pass_repeat'];

// Walidacja z instrukcji
if ($pass !== $pass_repeat) {
    die("Hasła nie są identyczne. Wróć.");
}

// --- DANE DO BAZY NA ZETOHOSTING ---
$nazwa_hosta = 'db1.zetohosting.pl';
$nazwa_usera = 'damskopb_lab';
$haslo_usera = 'FJpNEk46QWr8eSsX8z9j';
$baza_usera = 'damskopb_lab';
// ---------------------------------------------

$link = mysqli_connect($nazwa_hosta, $nazwa_usera, $haslo_usera, $baza_usera);
if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); }

mysqli_query($link, "SET NAMES 'utf8'");

// NIEBEZPIECZNE ZAPYTANIE INSERT Z INSTRUKCJI
$sql = "INSERT INTO users (username, password) VALUES ('$user', '$pass')";

if (mysqli_query($link, $sql)) {
    echo "Dodano nowego użytkownika: $user";
} else {
    echo "Błąd: " . mysqli_error($link);
}

mysqli_close($link);
?>
<br>
<a href="index3.php">Przejdź do logowania</a>
</BODY>
</HTML>