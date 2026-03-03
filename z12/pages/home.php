<!--
  Plik: pages/home.php
  Cel: Główny pulpit (dashboard) aplikacji.
  Funkcjonalność: Prezentuje menu wyboru modułów SCADA i Statystyk.
  Wykorzystane biblioteki: Bootstrap Icons.
  Sposób działania: Wyświetla interaktywne karty (Cards) z opisami i przyciskami prowadzącymi do głównych funkcjonalności systemu.
-->
<div class="p-5 mb-4 bg-light rounded-3 shadow-sm border">
    <div class="container-fluid py-3 text-center">
        <h1 class="display-5 fw-bold text-primary">Panel Sterowania Lab 12</h1>
        <p class="col-md-8 mx-auto fs-5 text-muted">Zintegrowany system monitorowania procesów SCADA oraz analityki IoT.</p>
        
        <?php if (isset($_SESSION['login_warning'])): ?>
            <div class="alert alert-warning mt-3 d-inline-block">
                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $_SESSION['login_warning']; unset($_SESSION['login_warning']); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">
    <!-- Karta SCADA -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm hover-shadow transition">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-primary bg-gradient text-white mb-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-speedometer2 fs-2"></i>
                </div>
                <h3 class="fw-bold">Monitor SCADA</h3>
                <p class="text-muted">Wizualizacja parametrów (v0-v5) na planie budynku, zegary Google Gauges oraz wykresy czasu rzeczywistego.</p>
                <a href="index.php?page=scada" class="btn btn-primary w-100 mt-2">Uruchom Monitor</a>
            </div>
        </div>
    </div>

    <!-- Karta Statystyk -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm hover-shadow transition">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-success bg-gradient text-white mb-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-graph-up fs-2"></i>
                </div>
                <h3 class="fw-bold">Analityka Wizyt</h3>
                <p class="text-muted">Podgląd danych tracker.js: geolokalizacja gości, informacje o przeglądarkach i rozdzielczości ekranu.</p>
                <a href="index.php?page=stats" class="btn btn-success w-100 mt-2">Pokaż Statystyki</a>
            </div>
        </div>
    </div>

    <!-- Karta Dokumentacji -->
    <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-dashed">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-secondary bg-gradient text-white mb-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-file-earmark-pdf fs-2"></i>
                </div>
                <h3 class="fw-bold">Instrukcje</h3>
                <p class="text-muted">Szczegółowe wytyczne dotyczące Części A (bez sterownika) oraz Części B (z Arduino).</p>
                <div class="d-grid gap-2 mt-2">
                    <a href="z12a SCADA 2025-03-17.pdf" target="_blank" class="btn btn-outline-dark btn-sm text-start">
                        <i class="bi bi-download"></i> Część A - SCADA/IoT
                    </a>
                    <a href="z12b SCADA 2025-03-24.pdf" target="_blank" class="btn btn-outline-dark btn-sm text-start">
                        <i class="bi bi-download"></i> Część B - Arduino
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .transition {
        transition: all 0.3s ease-in-out;
    }
    .border-dashed {
        border-style: dashed !important;
    }
</style>
