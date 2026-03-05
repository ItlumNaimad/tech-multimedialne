<!--
  Plik: pages/home.php
  Cel: Główny pulpit (dashboard) aplikacji.
  Funkcjonalność: Prezentuje menu wyboru modułów oraz status ciasteczek użytkownika.
-->
<div class="p-5 mb-4 bg-light rounded-3 shadow-sm border position-relative overflow-hidden">
    <!-- Dekoracyjny element tła -->
    <div class="position-absolute top-0 end-0 p-3 opacity-25">
        <i class="bi bi-shield-check display-1 text-primary"></i>
    </div>

    <div class="container-fluid py-2 text-center position-relative">
        <h1 class="display-5 fw-bold text-primary">Panel Sterowania Lab 12</h1>
        <p class="col-md-8 mx-auto fs-5 text-muted">Zintegrowany system monitorowania procesów SCADA oraz analityki IoT.</p>
        
        <div class="d-flex justify-content-center gap-2 mt-3">
            <?php if (isset($_SESSION['login_warning'])): ?>
                <div class="alert alert-warning d-inline-block py-1 px-3 mb-0">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $_SESSION['login_warning']; unset($_SESSION['login_warning']); ?>
                </div>
            <?php endif; ?>

            <!-- Status Ciasteczek (dla estetyki i informacji) -->
            <div id="cookie-status-badge" class="badge bg-info d-flex align-items-center gap-2 py-2 px-3">
                <i class="bi bi-cookie"></i> Status Cookies: <span id="cookie-state">Sprawdzanie...</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Karta SCADA -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm hover-shadow transition border-0">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-primary bg-gradient text-white mb-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-speedometer2 fs-2"></i>
                </div>
                <h3 class="fw-bold h5">Monitor SCADA</h3>
                <p class="text-muted small">Wizualizacja parametrów (x1-x5) na planie budynku i wykresy.</p>
                <a href="index.php?page=scada" class="btn btn-primary w-100 mt-2">Monitor</a>
            </div>
        </div>
    </div>

    <!-- Karta Symulatora -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm hover-shadow transition border-0">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-warning bg-gradient text-white mb-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-broadcast fs-2"></i>
                </div>
                <h3 class="fw-bold h5">Symulator</h3>
                <p class="text-muted small">Ręczne wprowadzanie danych czujników i stanów alarmowych.</p>
                <a href="index.php?page=formularz" class="btn btn-warning w-100 mt-2">Uruchom</a>
            </div>
        </div>
    </div>

    <!-- Karta Statystyk -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm hover-shadow transition border-0">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-success bg-gradient text-white mb-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-people-fill fs-2"></i>
                </div>
                <h3 class="fw-bold h5">Analityka Wizyt</h3>
                <p class="text-muted small">Podgląd danych tracker.js: lokalizacja i parametry techniczne.</p>
                <a href="index.php?page=stats" class="btn btn-success w-100 mt-2">Statystyki</a>
            </div>
        </div>
    </div>

    <!-- Karta Dokumentacji -->
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 shadow-sm border-dashed">
            <div class="card-body text-center p-4">
                <div class="feature-icon bg-secondary bg-gradient text-white mb-3 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="bi bi-file-earmark-pdf fs-2"></i>
                </div>
                <h3 class="fw-bold h5">Instrukcje</h3>
                <p class="text-muted small">Szczegółowe wytyczne techniczne dla zadań Lab 12.</p>
                <div class="d-grid gap-2 mt-2">
                    <a href="z12a SCADA 2025-03-17.pdf" target="_blank" class="btn btn-outline-dark btn-sm text-start">
                        <i class="bi bi-download"></i> Część A
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important;
    }
    .transition {
        transition: all 0.3s ease-in-out;
    }
    .border-dashed {
        border-style: dashed !important;
        background-color: transparent;
    }
</style>

<script>
    // Prosty skrypt wyświetlający status cookies na dashboardzie
    document.addEventListener('DOMContentLoaded', function() {
        const stateEl = document.getElementById('cookie-state');
        const badgeEl = document.getElementById('cookie-status-badge');
        
        if (navigator.cookieEnabled) {
            stateEl.innerText = 'Włączone';
            badgeEl.classList.replace('bg-info', 'bg-success');
        } else {
            stateEl.innerText = 'Wyłączone';
            badgeEl.classList.replace('bg-info', 'bg-danger');
        }
    });
</script>