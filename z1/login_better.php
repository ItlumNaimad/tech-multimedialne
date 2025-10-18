<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Damian Skonieczny - Logowanie (Bezpieczne)</title>
</head>
<body>
<h2>Bezpieczny Formularz Logowania (Mentor)</h2>
<p>Zaloguj się na konto utworzone przez bezpieczną rejestrację.</p>
<h2> Użytkownicy utworzeni inaczej niż przez plik rejestracja_better.php (bezpieczniejsze) nie mogą się zalogować przez ten formularz!</h2>
<form action="../src/login_handler.php" method="post">
    <div>
        <label for="username">Nazwa użytkownika:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <br>
    <div>
        <label for="password">Hasło:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <br>
    <div>
        <input type="submit" value="Zaloguj się">
    </div>
</form>

<br>
<p>Nie masz konta? <a href="rejestracja_better.php">Zarejestruj się tutaj</a>.</p>

</body>
</html>
