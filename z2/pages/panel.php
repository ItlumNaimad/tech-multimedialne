<?php
// Dodatkowe zabezpieczenie: jeśli ktoś tu wejdzie bez zalogowania, wyrzuć go
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php?page=logowanie');
    exit();
}
?>

<div class="container">
    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p class="col-md-8 fs-4">
                Jesteś poprawnie zalogowany w systemie opartym o Bootstrap.
                Twoja sesja jest aktywna. Możesz teraz przejść do innych części serwisu lub się wylogować.
            </p>
            <a class="dropdown-item" href="../../z3/index.php?page=home">
                <i class="bi bi-box-arrow-right me-2"></i>Przejdź do strony 3
            </a>
            <a href="../z2/index.php?page=wyloguj" class="btn btn-primary btn-lg" role="button">Wyloguj się</a>
        </div>
    </div>
</div>