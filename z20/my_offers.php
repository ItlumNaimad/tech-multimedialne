<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = getCurrentUserId();

// Usuwanie ogłoszenia
if (isset($_POST['delete_offer_id'])) {
    $del_id = (int)$_POST['delete_offer_id'];
    
    // Upewnijmy się, że to jego ogłoszenie (lub jest adminem)
    $check = $pdo->prepare("SELECT id FROM offers WHERE id = ? AND user_id = ?");
    $check->execute([$del_id, $user_id]);
    if ($check->fetch() || isModerator()) {
        $stmtDel = $pdo->prepare("DELETE FROM offers WHERE id = ?");
        $stmtDel->execute([$del_id]);
        $success = "Ogłoszenie zostało usunięte.";
    } else {
        $error = "Brak uprawnień do usunięcia tego ogłoszenia.";
    }
}

$stmt = $pdo->prepare("SELECT o.*, c.name as category_name,
                      (SELECT filename FROM offer_photos op WHERE op.offer_id = o.id ORDER BY id ASC LIMIT 1) as main_photo
                      FROM offers o
                      JOIN categories c ON o.category_id = c.id
                      WHERE o.user_id = ?
                      ORDER BY o.created_at DESC");
$stmt->execute([$user_id]);
$offers = $stmt->fetchAll();

require_once 'header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Moje Ogłoszenia</h2>
    <a href="offer_create.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Dodaj nowe ogłoszenie</a>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row row-cols-1 g-4">
    <?php if (count($offers) > 0): ?>
        <?php foreach ($offers as $offer): ?>
            <div class="col">
                <div class="card shadow-sm border-0 flex-md-row">
                    <?php if ($offer['main_photo']): ?>
                        <img src="uploads/<?= htmlspecialchars($offer['main_photo']) ?>" class="img-fluid rounded-start" style="width: 200px; object-fit: cover;" alt="Zdjęcie nieruchomości">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex justify-content-center align-items-center rounded-start" style="width: 200px; height: 200px;">Brak zdjęcia</div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <span class="badge bg-primary text-uppercase"><?= htmlspecialchars($offer['category_name']) ?></span>
                                <span class="badge bg-secondary text-uppercase"><?= htmlspecialchars($offer['type']) ?></span>
                            </div>
                            <span class="text-muted"><i class="bi bi-calendar"></i> <?= date('d.m.Y', strtotime($offer['created_at'])) ?></span>
                        </div>
                        <h4 class="card-title"><?= htmlspecialchars($offer['title']) ?></h4>
                        <p class="card-text text-muted mb-2">
                            <i class="bi bi-geo-alt-fill text-danger"></i> <?= htmlspecialchars($offer['city']) ?>, <?= htmlspecialchars($offer['zipcode']) ?>
                        </p>
                        <h5 class="text-success fw-bold"><?= number_format($offer['price'], 2, ',', ' ') ?> PLN</h5>
                        
                        <div class="mt-auto d-flex gap-2">
                            <a href="offer.php?id=<?= $offer['id'] ?>" class="btn btn-primary"><i class="bi bi-eye"></i> Zobacz</a>
                            
                            <form method="post" class="d-inline" onsubmit="return confirm('Czy na pewno chcesz usunąć to ogłoszenie?');">
                                <input type="hidden" name="delete_offer_id" value="<?= $offer['id'] ?>">
                                <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i> Usuń</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">Nie masz jeszcze żadnych ogłoszeń.</div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
