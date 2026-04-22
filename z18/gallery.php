<?php
require_once 'db_connect.php';
require_once 'header.php';

$gallery_id = $_GET['id'] ?? null;
if (!$gallery_id) {
    echo "Nie podano ID galerii."; require_once 'footer.php'; exit;
}

$stmt = $pdo->prepare("SELECT g.*, u.username FROM galleries g JOIN users u ON g.user_id = u.id WHERE g.id = ?");
$stmt->execute([$gallery_id]);
$gallery = $stmt->fetch();

if (!$gallery) {
    echo "Galeria nie istnieje."; require_once 'footer.php'; exit;
}

// Sprawdzenie dostępu: jeśli PRIVATE to tylko autor i admin mogą wejść
if ($gallery['visibility'] == 'private' && (getCurrentUserId() != $gallery['user_id'] && !isAdmin())) {
    echo "<div class='alert alert-danger'>Ta galeria jest prywatna. Brak dostępu.</div>";
    require_once 'footer.php'; exit;
}

// Sprawdzenie moderacji usuwania zdjęcia i samej galerii
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && (getCurrentUserId() == $gallery['user_id'] || isModerator())) {
    if ($_POST['action'] == 'delete_photo') {
        $photo_id = $_POST['photo_id'];
        $stmt = $pdo->prepare("SELECT filename FROM photos WHERE id = ?");
        $stmt->execute([$photo_id]);
        $photo = $stmt->fetch();
        if ($photo) {
            $pdo->prepare("DELETE FROM photos WHERE id = ?")->execute([$photo_id]);
            @unlink(__DIR__ . '/' . $photo['filename']);
        }
        header("Location: gallery.php?id=$gallery_id");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM photos WHERE gallery_id = ? ORDER BY created_at DESC");
$stmt->execute([$gallery_id]);
$photos = $stmt->fetchAll();

$can_delete = getCurrentUserId() == $gallery['user_id'] || isModerator();
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php">Galerie</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($gallery['title']) ?></li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><?= htmlspecialchars($gallery['title']) ?> <small class="text-muted fs-5">(<?= htmlspecialchars($gallery['username']) ?>)</small></h2>
    <?php if ($can_delete && $gallery['visibility'] == 'public'): ?>
        <a href="upload.php" class="btn btn-outline-success">Dodaj zdjęcie do tej galerii</a>
    <?php endif; ?>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mt-2">
    <?php foreach($photos as $p): ?>
    <div class="col">
        <div class="card h-100 shadow-sm gallery-card">
            <!-- Lightbox concept: simple display linked to detailed view page -->
            <a href="photo.php?id=<?= $p['id'] ?>">
                <img src="<?= htmlspecialchars($p['filename']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p['title']) ?>">
            </a>
            <div class="card-body">
                <p class="card-text fw-bold text-truncate mb-1"><?= htmlspecialchars($p['title']) ?></p>
                <?php if ($can_delete): ?>
                    <form method="post" onsubmit="return confirm('Trwale usunąć to zdjęcie?');">
                        <input type="hidden" name="action" value="delete_photo">
                        <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                        <button class="btn btn-sm btn-danger w-100 mt-2"><i class="bi bi-trash"></i> Usuń</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-footer p-2 text-center">
                <a href="photo.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary">Szczegóły / Oceny</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if(empty($photos)): ?>
        <div class="col-12"><p class="text-muted">Ta galeria nie posiada jeszcze żadnych zdjęć.</p></div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
