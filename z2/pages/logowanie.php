<div class="container" style="max-width: 400px;">
    <main class="form-signin w-100 m-auto text-center">
        <form action="../src/login_handler.php" method="post">
            <img class="mb-4" src="https://getbootstrap.com/docs/5.3/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
            <h1 class="h3 mb-3 fw-normal">Zaloguj się (Bezpiecznie)</h1>

            <div class="form-floating mb-2">
                <input type="text" class="form-control" id="username" name="username" placeholder="Nazwa użytkownika" required>
                <label for="username">Nazwa użytkownika</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="Hasło" required>
                <label for="password">Hasło</label>
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Zaloguj się</button>

            <p class="mt-3 mb-1">
                Nie masz konta? <a href="index.php?page=rejestracja">Zarejestruj się</a>
            </p>
        </form>
    </main>
</div>