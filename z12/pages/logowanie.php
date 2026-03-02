<div class="container" style="max-width: 400px;">
    <main class="form-signin w-100 m-auto text-center bg-white p-4 rounded shadow-sm">
        <form action="database/login_handler.php" method="post">
            <i class="bi bi-person-circle fs-1 text-primary mb-4"></i>

            <h1 class="h3 mb-3 fw-normal">Zaloguj się</h1>

            <div class="form-floating mb-2 text-start">
                <input type="text" class="form-control" id="username" name="username" placeholder="Nazwa użytkownika" required>
                <label for="username">Nazwa użytkownika</label>
            </div>
            <div class="form-floating mb-3 text-start">
                <input type="password" class="form-control" id="password" name="password" placeholder="Hasło" required>
                <label for="password">Hasło</label>
            </div>

            <input type="hidden" id="screen_res" name="screen_res" value="">

            <input type="hidden" name="redirect_url" value="../index.php?page=home">

            <button class="w-100 btn btn-lg btn-primary" type="submit">Zaloguj się</button>

            <p class="mt-3 mb-1">
                Nie masz konta? <a href="index.php?page=rejestracja">Zarejestruj się</a>
            </p>
        </form>
    </main>
    <script>
        (function() {
            document.getElementById('screen_res').value = screen.width + 'x' + screen.height;
        })();
    </script>
</div>