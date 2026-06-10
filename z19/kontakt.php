<?php
require_once 'auth.php';
require_once 'db_connect.php';
require_once 'header.php';
?>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-telephone"></i> Kontakt</h3>
                <ul class="list-unstyled mt-4">
                    <li class="mb-3"><strong><i class="bi bi-building"></i> Firma:</strong> Zbijanie Bąków S.A.</li>
                    <li class="mb-3"><strong><i class="bi bi-geo-alt"></i> Adres:</strong> ul. Leniwa 15, 85-000 Bydgoszcz</li>
                    <li class="mb-3"><strong><i class="bi bi-telephone-fill"></i> Telefon:</strong> +48 123 456 789</li>
                    <li class="mb-3"><strong><i class="bi bi-envelope"></i> E-mail:</strong> biuro@zbijanie-bakow.pl</li>
                    <li><strong><i class="bi bi-facebook"></i> Facebook:</strong> facebook.com/zbijaniebakow</li>
                </ul>
            </div>
        </div>

        <!-- Symulacja Wtyczki Geolokalizacyjnej -->
        <div class="card shadow-sm bg-light border-info">
            <div class="card-body">
                <h5 class="text-info"><i class="bi bi-globe"></i> Moduł Geolokalizacji (Plugin)</h5>
                <p class="small mb-2">Narzędzie identyfikujące fizyczną lokalizację na podstawie adresu IP.</p>
                <div id="geo-results" class="fw-bold">
                    <div class="spinner-border spinner-border-sm text-info" role="status"></div> Trwa namierzanie...
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title"><i class="bi bi-map"></i> Jak do nas dotrzeć</h3>
                <p class="text-muted">Znajdujemy się w samym sercu miasta. Zapraszamy w godzinach 12:00 - 12:15.</p>
                <div class="ratio ratio-16x9 rounded" style="overflow: hidden;">
                    <iframe src="https://maps.google.com/maps?q=Bydgoszcz+Politechnika+Bydgoska&t=&z=15&ie=UTF8&iwloc=&output=embed" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Geolokalizacja IP – użycie JSON endpoint bez CORS (ip-api darmowy endpoint)
document.addEventListener('DOMContentLoaded', function() {
    fetch('https://ip-api.com/json/?fields=status,country,city,query')
        .then(response => response.json())
        .then(data => {
            const geoDiv = document.getElementById('geo-results');
            if(data.status === 'success') {
                geoDiv.innerHTML = `
                    <span class="text-success"><i class="bi bi-check-circle-fill"></i> Sukces!</span><br>
                    Wykryty Kraj: ${data.country}<br>
                    Wykryte Miasto: ${data.city}<br>
                    Twój adres IP: ${data.query}
                `;
            } else {
                geoDiv.innerHTML = `<span class="text-danger">Nie udało się pobrać lokalizacji. Używasz adblocka lub VPN?</span>`;
            }
        })
        .catch(error => {
            document.getElementById('geo-results').innerHTML = `<span class="text-danger">Błąd API geolokalizacji.</span>`;
        });
});
</script>

<?php require_once 'footer.php'; ?>
