<div class="container" style="max-width: 500px;">
    <main class="form-registration w-100 m-auto">
        <h1 class="h3 mb-3 fw-normal text-center">Zarejestruj się (Bezpiecznie)</h1>

        <form action="../src/rejestracja_handler.php" method="post">

            <div class="form-floating mb-2">
                <input type="text" class="form-control" id="username" name="username" placeholder="Nazwa użytkownika" required>
                <label for="username">Nazwa użytkownika</label>
            </div>

            <div class="form-floating mb-2">
                <input type="password" class="form-control" id="password" name="password" placeholder="Hasło" required>
                <label for="password">Hasło</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password_repeat" name="password_repeat" placeholder="Powtórz hasło" required>
                <label for="password_repeat">Powtórz hasło</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Zarejestruj się</button>

            <p class="mt-3 mb-1 text-center">
                Masz już konto? <a href="index.php?page=logowanie">Zaloguj się</a>
            </p>
        </form>
    </main>
</div>