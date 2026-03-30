<?php
/**
 * Plik: pages/logowanie.php
 * Cel: Formularz logowania dla pracowników.
 */
session_start();
if (isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie - ToDo System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .login-container { max-width: 400px; margin-top: 100px; }
    </style>
</head>
<body>

<div class="container login-container">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white text-center py-3">
            <h4 class="mb-0">Logowanie</h4>
        </div>
        <div class="card-body p-4">
            <!-- Miejsce na reklamę AdSense -->
            <div class="adsense-box mb-4 text-center border-bottom pb-3">
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-1234567890123456"
                     data-ad-slot="2222222222"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
                <small class="text-muted d-block mt-1">Reklama</small>
            </div>

            <form action="../database/login_handler.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Użytkownik</label>
                    <input type="text" name="username" class="form-control" required placeholder="Wpisz login">
                </div>
                <div class="mb-3">
                    <label class="form-label">Hasło</label>
                    <input type="password" name="password" class="form-control" required placeholder="Wpisz hasło">
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mt-2">Zaloguj się</button>
            </form>
            <div class="mt-3 text-center">
                <p class="mb-0">Nie masz konta?</p>
                <a href="rejestracja.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">Zarejestruj się</a>
            </div>
        </div>
        <div class="card-footer text-center bg-white border-0 pb-4">
            <small class="text-muted">Dostęp tylko dla pracowników firmy.</small>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
