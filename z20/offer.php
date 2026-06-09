<?php
require_once 'auth.php';
require_once 'db_connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Nieprawidłowy ID oferty.");
}

$stmt = $pdo->prepare("SELECT o.*, c.name as category_name, u.username 
                       FROM offers o 
                       JOIN categories c ON o.category_id = c.id 
                       JOIN users u ON o.user_id = u.id
                       WHERE o.id = ?");
$stmt->execute([$id]);
$offer = $stmt->fetch();

if (!$offer) {
    die("Nie znaleziono ogłoszenia.");
}

$stmtPhotos = $pdo->prepare("SELECT * FROM offer_photos WHERE offer_id = ?");
$stmtPhotos->execute([$id]);
$photos = $stmtPhotos->fetchAll();

require_once 'header.php';
?>

<div class="row">
    <div class="col-lg-8">
        <!-- Galeria zdjęć -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <?php if (count($photos) > 0): ?>
                    <div id="offerCarousel" class="carousel slide" data-bs-ride="carousel">
                      <div class="carousel-inner" style="border-radius: 10px; overflow: hidden; background: #000;">
                        <?php foreach ($photos as $index => $photo): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                              <img src="uploads/<?= htmlspecialchars($photo['filename']) ?>" class="d-block w-100" style="height: 500px; object-fit: contain;" alt="Zdjęcie oferty">
                            </div>
                        <?php endforeach; ?>
                      </div>
                      <?php if (count($photos) > 1): ?>
                      <button class="carousel-control-prev" type="button" data-bs-target="#offerCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Poprzednie</span>
                      </button>
                      <button class="carousel-control-next" type="button" data-bs-target="#offerCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Następne</span>
                      </button>
                      <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-secondary text-center m-0">Brak zdjęć dla tego ogłoszenia.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Opis -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <h4 class="card-title fw-bold border-bottom pb-2">Opis</h4>
                <p class="mt-3" style="white-space: pre-line;"><?= htmlspecialchars($offer['description']) ?></p>
            </div>
        </div>
        
        <!-- Mapa i Geoportal -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <h4 class="card-title fw-bold border-bottom pb-2">Lokalizacja i Mapy</h4>
                
                <div class="mb-3 mt-3">
                    <strong>Adres:</strong> <?= htmlspecialchars($offer['city']) ?>, <?= htmlspecialchars($offer['zipcode']) ?>
                    <?php if ($offer['address']) echo "<br>".htmlspecialchars($offer['address']); ?>
                </div>

                <?php 
                // Przygotowanie linku do Google Maps z pinezką (markerem)
                $mapQuery = "";
                if (!empty($offer['lat']) && !empty($offer['lng'])) {
                    $mapQuery = $offer['lat'] . "," . $offer['lng'];
                } else {
                    $mapQuery = urlencode($offer['city'] . " " . $offer['address']);
                }
                $googleMapsIframeUrl = "https://maps.google.com/maps?q=" . $mapQuery . "&t=&z=15&ie=UTF8&iwloc=&output=embed";
                $googleMapsLink = "https://maps.google.com/?q=" . $mapQuery;
                ?>
                
                <div class="ratio ratio-16x9 mb-3 rounded" style="overflow: hidden;">
                    <iframe src="<?= $googleMapsIframeUrl ?>" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                </div>
                <div class="text-end mb-4">
                    <a href="<?= $googleMapsLink ?>" target="_blank" class="btn btn-outline-primary btn-sm"><i class="bi bi-map-fill"></i> Otwórz w Google Maps</a>
                </div>

                <?php if (!empty($offer['geoportal_url'])): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill"></i> Sprzedawca udostępnił link do Geoportalu dla tej nieruchomości. Pozwala on na sprawdzenie granic działki, uzbrojenia terenu i obszarów chronionych.
                    <br><br>
                    <a href="<?= htmlspecialchars($offer['geoportal_url']) ?>" target="_blank" class="btn btn-info text-white"><i class="bi bi-globe"></i> Zobacz na Geoportalu</a>
                </div>
                <?php else: ?>
                    <?php 
                        // Domyślny geoportal link oparty na koordynatach, jeśli podane
                        if (!empty($offer['lat']) && !empty($offer['lng'])) {
                            $geoLink = "https://mapy.geoportal.gov.pl/imap/Imgp_2.html?locale=pl&gui=new&sessionID=&bbox=" . ($offer['lng'] - 0.01) . "," . ($offer['lat'] - 0.01) . "," . ($offer['lng'] + 0.01) . "," . ($offer['lat'] + 0.01);
                            echo '<div class="alert alert-secondary"><i class="bi bi-compass"></i> Zobacz współrzędne w państwowym systemie GIS: <br><br><a href="'.$geoLink.'" target="_blank" class="btn btn-secondary btn-sm"><i class="bi bi-globe"></i> Otwórz Geoportal (przybliżona lokalizacja)</a></div>';
                        }
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Szczegóły po prawej -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <span class="badge bg-primary mb-2"><?= htmlspecialchars($offer['category_name']) ?></span>
                <span class="badge bg-secondary mb-2"><?= htmlspecialchars($offer['type']) ?></span>
                <h3 class="fw-bold mb-3"><?= htmlspecialchars($offer['title']) ?></h3>
                
                <h2 class="text-success fw-bold mb-4"><?= number_format($offer['price'], 2, ',', ' ') ?> PLN</h2>
                
                <ul class="list-group list-group-flush mb-4">
                    <?php if ($offer['area']): ?>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Powierzchnia</span>
                        <span class="fw-bold"><?= htmlspecialchars($offer['area']) ?> m²</span>
                    </li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Wystawiający</span>
                        <span class="fw-bold"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($offer['username']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Dodano</span>
                        <span class="fw-bold"><?= date('d.m.Y', strtotime($offer['created_at'])) ?></span>
                    </li>
                </ul>
                
                <button class="btn btn-primary w-100 btn-lg mb-2"><i class="bi bi-telephone-fill"></i> Pokaż numer telefonu</button>
                <button class="btn btn-outline-secondary w-100 btn-lg"><i class="bi bi-envelope-fill"></i> Napisz wiadomość</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
