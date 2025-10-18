<?php
// Na samej górze pliku, PRZED jakimkolwiek kodem HTML, musimy uruchomić sesję
session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<HEAD>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</HEAD>
<BODY>
<?php
$user=$_POST['user'];
$pass=$_POST['pass'];

// --- UZUPEŁNIJ SWOJE DANE DO LOKALNEJ BAZY ---
$nazwa_hosta = 'localhost';
$nazwa_usera = 'root';
$haslo_usera = '';
$baza_usera = 'z1_damsko';
// ---------------------------------------------

$link = mysqli_connect($nazwa_hosta, $nazwa_usera, $haslo_usera, $baza_usera);
if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); }

mysqli_query($link, "SET NAMES 'utf8'");

$result = mysqli_query($link, "SELECT * FROM users WHERE username='$user'");
$rekord = mysqli_fetch_array($result);

if(!$rekord)
{
    mysqli_close($link);
    echo "Brak użytkownika o takim loginie!";
    // Wg diagramu, powinniśmy wrócić do logowania [cite: 321]
    // header('Location: index3.php');
}
else // jeśli $rekord istnieje
{
    if($rekord['password']==$pass) // czy hasło zgadza się z BD
    {
        // --- Logowanie poprawne ---
        // Ustawiamy zmienną sesyjną [cite: 317]
        $_SESSION['loggedin'] = true;

        // Przekierowujemy do "tajnej" strony [cite: 318]
        header('Location: index4.php');
        exit(); // WAŻNE: zatrzymujemy wykonywanie skryptu po przekierowaniu
    }
    else
    {
        mysqli_close($link);
        echo "Błąd w haśle!";
        // Wg diagramu, powinniśmy wrócić do logowania [cite: 321]
        // header('Location: index3.php');
    }
}
?>
</BODY>
</HTML>
