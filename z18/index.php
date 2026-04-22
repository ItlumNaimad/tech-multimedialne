<?php
require_once 'db_connect.php';
require_once 'header.php';

$type = $_GET['type'] ?? 'all';

$sql = "SELECT g.*, u.username FROM galleries g JOIN users u ON g.user_id = u.id WHERE g.visibility != 'private'";
$params = [];

if ($type == 'public') {
    $sql .= " AND g.visibility = 'public'";
} elseif ($type == 'commercial') {
    $sql .= " AND g.visibility = 'commercial'";
}
$sql .= " ORDER BY g.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$galleries = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Przegląd Galerii</h2>
    <div>
        <span class="badge bg-success">Publiczne</span>
        <span class="badge bg-warning text-dark">Komercyjne</span>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php foreach($galleries as $g): ?>
    <div class="col">
        <div class="card h-100 shadow-sm border-<?= $g['visibility'] == 'commercial' ? 'warning' : 'success' ?>">
            <div class="card-body">
                <h5 class="card-title text-truncate">
                    <a href="gallery.php?id=<?= $g['id'] ?>" class="text-decoration-none text-dark stretched-link">
                        <?= htmlspecialchars($g['title']) ?>
                    </a>
                </h5>
                <p class="card-text text-muted mb-1">
                    <i class="bi bi-person-circle"></i> <?= htmlspecialchars($g['username']) ?>
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted"><i class="bi bi-calendar3"></i> <?= date('Y-m-d', strtotime($g['created_at'])) ?></small>
                    <span class="badge bg-<?= $g['visibility'] == 'commercial' ? 'warning text-dark' : 'success' ?>">
                        <?= $g['visibility'] == 'commercial' ? 'Komercyjna' : 'Publiczna' ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(empty($galleries)): ?>
        <div class="col-12"><p class="text-muted">Brak dostępnych galerii w zadanych kryteriach.</p></div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
