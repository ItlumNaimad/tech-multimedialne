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

            <<input type="hidden" id="screen_res" name="screen_res" value="">
            <input type="hidden" id="window_res" name="window_res" value="">
            <input type="hidden" id="colors" name="colors" value="">
            <input type="hidden" id="cookies_enabled" name="cookies_enabled" value="">
            <input type="hidden" id="java_enabled" name="java_enabled" value="">
            <input type="hidden" id="language" name="language" value="">

            <button class="w-100 btn btn-lg btn-primary" type="submit">Zaloguj się</button>

            <p class="mt-3 mb-1">
                Nie masz konta? <a href="index.php?page=rejestracja">Zarejestruj się</a>
            </p>
        </form>
    </main>
    <script>
        // Ten skrypt uruchomi się automatycznie po załadowaniu strony
        (function() {
            // Zbieramy dane o ekranie
            document.getElementById('screen_res').value = screen.width + 'x' + screen.height;
            document.getElementById('window_res').value = window.innerWidth + 'x' + window.innerHeight;
            document.getElementById('colors').value = screen.colorDepth;

            // Zbieramy dane o przeglądarce
            document.getElementById('cookies_enabled').value = navigator.cookieEnabled ? 'Tak' : 'Nie';
            // metoda .javaEnabled() została oznaczona przez IDE jako "deprecated"
            document.getElementById('java_enabled').value = navigator.javaEnabled() ? 'Tak' : 'Nie';
            document.getElementById('language').value = navigator.language || navigator.userLanguage;
        })();
    </script>
</div>