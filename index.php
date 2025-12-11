<?php
session_start();
// Czyścimy wszystkie zmienne sesyjne
$_SESSION = array();

// Jeśli sesja korzysta z ciasteczek, usuń także ciasteczko sesyjne
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
    );
}
// Ostatecznie niszczymy sesję
session_destroy();
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Damian Skonieczny</title>
<body>
<a href ="z1">Logowanie, Sesja, Rejestracja</a><br />
<a href ="z2">Zadanie 2</a><br />
<a href ="z3">Zadanie 3</a><br />
<a href ="z4">Zadanie 4</a><br />
<a href ="z5">Zadanie 5</a><br />
<a href ="z6a">Zadanie 6 A</a><br />
<a href ="z6b">Zadanie 6 B</a><br />
<a href ="z7">Zadanie 7</a><br />
<a href ="z8">Zadanie 8</a><br />
<a href ="z9">Zadanie 9</a><br />
<a href ="z10">Zadanie 10</a><br />
<a href ="z11">Zadanie 11</a><br />
<a href ="z12">Zadanie 12</a><br />
<a href ="projekt">Projekt</a><br />
</body>
