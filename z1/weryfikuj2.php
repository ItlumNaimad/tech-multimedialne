<?php

// Wersja poprawiona, z częściowym wyeliminowaniem podatności
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>
<?php
$user = htmlentities ($_POST['user'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $user
$pass = htmlentities ($_POST['pass'], ENT_QUOTES, "UTF-8"); // rozbrojenie potencjalnej bomby w zmiennej $pass

// --- DANE DO BAZY NA ZETOHOSTING ---
$nazwa_hosta = '127.0.0.1';
$nazwa_usera = '';
$haslo_usera = '';
$baza_usera = '';
// ---------------------------------------------

$link = mysqli_connect($nazwa_hosta, $nazwa_usera, $haslo_usera, $baza_usera);
if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD

mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków

// 1. NAJPIERW pobieramy wiersz, w którym login zgadza się z tym z formularza
$result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'");
$rekord = mysqli_fetch_array($result); // wiersz z BD

if(!$rekord) //Jeśli brak, to nie ma użytkownika o podanym loginie
{
    mysqli_close($link);
    echo "Brak użytkownika o takim loginie !"; // UWAGA: Zła praktyka (podpowiadanie hakerowi) [cite: 245, 269]
}
else // jeśli $rekord istnieje
{
    // 2. DOPIERO TERAZ porównujemy hasło z formularza z hasłem z bazy
    if($rekord['password']==$pass) // czy hasło zgadza się z BD
    {
        echo "Logowanie Ok. User: {$rekord['username']}. Hasło: {$rekord['password']}";
    }
    else
    {
        mysqli_close($link);
        echo "Błąd w haśle !"; // UWAGA: Zła praktyka (podpowiadanie hakerowi) [cite: 260, 266]
    }
}
?>
</BODY>
</HTML>