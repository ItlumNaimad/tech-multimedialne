<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Damian Skonieczny</title> </head>
<body>
<h2>Formularz Rejestracji</h2>
<p>Załóż nowe konto, aby móc się zalogować.</p>
<h2>Nie będziesz mógł zalogować się tym użytkownikiem nigdzie z wyjątkiem login_better.php (bezpieczniejsze)</h2>
<form action="../src/register_handler.php" method="post">
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
        <label for="password_repeat">Powtórz hasło:</label>
        <input type="password" id="password_repeat" name="password_repeat" required>
    </div>
    <br>
    <div>
        <input type="submit" value="Zarejestruj się">
    </div>
</form>

<br>
<p>Masz już konto? <a href="logowanie.php">Zaloguj się tutaj</a>.</p>

</body>
</html>