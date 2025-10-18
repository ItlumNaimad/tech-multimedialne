<?php
// UWAGA: TEN SKRYPT JEST CELOWO PODATNY NA SQL INJECTION
// Nigdy nie używaj go w prawdziwej aplikacji!
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Damian Skonieczny - index1 (Podatny)</title>
</HEAD>
<BODY>
<?php
$user=$_POST['user']; // login z formularza
$pass=$_POST['pass']; // hasło z formularza

// --- UZUPEŁNIJ SWOJE DANE DO LOKALNEJ BAZY ---
$nazwa_hosta = 'localhost';
$nazwa_usera = 'root';
$haslo_usera = '';
$baza_usera = 'z1_damsko';
// ---------------------------------------------

$link = mysqli_connect($nazwa_hosta, $nazwa_usera, $haslo_usera, $baza_usera);
if(!$link) { echo"Error: ". mysqli_connect_errno()." ".mysqli_connect_error(); } // obsługa błędu połączenia z BD

mysqli_query($link, "SET NAMES 'utf8'"); // ustawienie polskich znaków

// TU JEST PIES POGRZEBANY (i błąd bezpieczeństwa)
$result = mysqli_query($link, "SELECT * FROM users WHERE (username='$user') and (password='$pass')");

$rekord = mysqli_fetch_array($result);
if(!$rekord) //Jeśli brak, to nie ma użytkownika o podanym loginie
{
    mysqli_close($link); //zamknięcie połączenia z BD
    echo "Blad nazwy użytkownika lub hasla";
}
else // Jeśli $rekord istnieje
{
    mysqli_close($link);
    echo "Logowanie Ok. User: {$rekord['username']}. Hasło: {$rekord['password']}";
}
?>
</BODY>
</HTML>
