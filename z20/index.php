<?php
require_once 'auth.php';
require_once 'db_connect.php';

// Filtrowanie po kategorii
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

$sql = "SELECT o.*, c.name as category_name,
        (SELECT filename FROM offer_photos op WHERE op.offer_id = o.id ORDER BY id ASC LIMIT 1) as main_photo
        FROM offers o
        JOIN categories c ON o.category_id = c.id";

$params = [];
if ($category_id) {
    $sql .= " WHERE o.category_id = ?";
    $params[] = $category_id;
}
$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$offers = $stmt->fetchAll();

require_once 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Najnowsze ogłoszenia nieruchomości</h2>
    <a href="offer_create.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Dodaj ogłoszenie</a>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if (count($offers) > 0): ?>
        <?php foreach ($offers as $offer): ?>
            <div class="col">
                <div class="card h-100 offer-card shadow-sm border-0">
                    <?php if ($offer['main_photo']): ?>
                        <img src="uploads/<?= htmlspecialchars($offer['main_photo']) ?>" class="card-img-top" alt="Zdjęcie nieruchomości">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex justify-content-center align-items-center" style="height:200px;">Brak zdjęcia</div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($offer['category_name']) ?></span>
                            <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($offer['type']) ?></span>
                        </div>
                        <h5 class="card-title text-truncate"><?= htmlspecialchars($offer['title']) ?></h5>
                        <p class="card-text text-muted mb-2">
                            <i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($offer['city']) ?>, <?= htmlspecialchars($offer['zipcode']) ?>
                        </p>
                        <h4 class="text-success fw-bold mb-3"><?= number_format($offer['price'], 2, ',', ' ') ?> PLN</h4>
                        
                        <div class="mt-auto">
                            <a href="offer.php?id=<?= $offer['id'] ?>" class="btn btn-outline-primary w-100">Zobacz szczegóły</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">Brak ogłoszeń w tej kategorii.</div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
