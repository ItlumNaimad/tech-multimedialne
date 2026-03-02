<div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
    <div class="container-fluid py-5 text-center">
        <h1 class="display-5 fw-bold text-primary">Witaj w Laboratorium 12!</h1>
        <p class="col-md-8 mx-auto fs-4">System logowania i rejestracji został pomyślnie skonfigurowany.</p>
        
        <?php if (isset($_SESSION['login_warning'])): ?>
            <div class="alert alert-warning mt-3">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $_SESSION['login_warning']; unset($_SESSION['login_warning']); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-success"><i class="bi bi-shield-check"></i> Bezpieczeństwo</h5>
                <p class="card-text">Hasła są haszowane za pomocą algorytmu <code>PASSWORD_DEFAULT</code> (bcrypt).</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-info"><i class="bi bi-geo-alt"></i> Geoblokada</h5>
                <p class="card-text">Dostęp do logowania jest ograniczony wyłącznie do adresów IP z Polski (PL).</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-danger"><i class="bi bi-lock"></i> Ochrona Brute-force</h5>
                <p class="card-text">Po 3 nieudanych próbach logowania konto zostaje tymczasowo zablokowane.</p>
            </div>
        </div>
    </div>
</div>