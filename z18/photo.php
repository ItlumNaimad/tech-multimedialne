<?php
require_once 'db_connect.php';
require_once 'header.php';

$photo_id = $_GET['id'] ?? null;
if (!$photo_id) {
    echo "Nie podano ID zdjęcia."; require_once 'footer.php'; exit;
}

$stmt = $pdo->prepare("SELECT p.*, g.user_id as owner_id, g.visibility, g.title as gallery_title, u.username as owner_name FROM photos p JOIN galleries g ON p.gallery_id = g.id JOIN users u ON g.user_id = u.id WHERE p.id = ?");
$stmt->execute([$photo_id]);
$photo = $stmt->fetch();

if (!$photo) {
    echo "Zdjęcie nie istnieje."; require_once 'footer.php'; exit;
}

if ($photo['visibility'] == 'private' && (getCurrentUserId() != $photo['owner_id'] && !isAdmin())) {
    echo "<div class='alert alert-danger'>Prywatne. Brak dostępu.</div>"; require_once 'footer.php'; exit;
}

// Rating action
$my_user_id = getCurrentUserId();
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isLoggedIn()) {
    if ($_POST['action'] == 'rate') {
        // Can rate only others
        if ($my_user_id != $photo['owner_id']) {
            $rating = (int)$_POST['rating'];
            if ($rating >= 1 && $rating <= 5) {
                // Upsert logic
                $stmt = $pdo->prepare("INSERT INTO ratings (photo_id, user_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating)");
                $stmt->execute([$photo_id, $my_user_id, $rating]);
            }
        }
        header("Location: photo.php?id=$photo_id"); exit;
    }
    elseif ($_POST['action'] == 'comment') {
        $content = trim($_POST['content']);
        if (!empty($content)) {
            $stmt = $pdo->prepare("INSERT INTO comments (photo_id, user_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$photo_id, $my_user_id, htmlspecialchars($content)]);
        }
        header("Location: photo.php?id=$photo_id"); exit;
    }
}

// Get average rating
$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(*) as count_rating FROM ratings WHERE photo_id = ?");
$stmt->execute([$photo_id]);
$ratingStat = $stmt->fetch();
$avg = number_format($ratingStat['avg_rating'] ?? 0, 1);
$count_r = $ratingStat['count_rating'];

// Get user rating
$my_rating = null;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT rating FROM ratings WHERE photo_id = ? AND user_id = ?");
    $stmt->execute([$photo_id, $my_user_id]);
    $my_r_row = $stmt->fetch();
    if ($my_r_row) $my_rating = $my_r_row['rating'];
}

// Get comments
$stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.photo_id = ? ORDER BY c.created_at ASC");
$stmt->execute([$photo_id]);
$comments = $stmt->fetchAll();
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php">Galerie</a></li>
    <li class="breadcrumb-item"><a href="gallery.php?id=<?= $photo['gallery_id'] ?>"><?= htmlspecialchars($photo['gallery_title']) ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($photo['title']) ?></li>
  </ol>
</nav>

<div class="row">
    <div class="col-md-8 mb-4 text-center">
        <a href="<?= htmlspecialchars($photo['filename']) ?>" target="_blank">
            <img src="<?= htmlspecialchars($photo['filename']) ?>" class="img-fluid border p-1 rounded bg-white shadow" alt="<?= htmlspecialchars($photo['title']) ?>">
        </a>
    </div>
    
    <div class="col-md-4">
        <h3><?= htmlspecialchars($photo['title']) ?></h3>
        <p class="text-muted">Autor: <?= htmlspecialchars($photo['owner_name']) ?><br>Dodano: <?= $photo['created_at'] ?></p>
        
        <div class="card mb-4 bg-light">
            <div class="card-body">
                <h5 class="card-title d-flex align-items-center">
                    <i class="bi bi-star-fill text-warning me-2"></i> 
                    <?= $avg ?> / 5.0
                </h5>
                <p class="mb-0 text-muted small">(Liczba głosów: <?= $count_r ?>)</p>
                
                <?php if (isLoggedIn() && $my_user_id != $photo['owner_id']): ?>
                    <form method="post" class="mt-3">
                        <input type="hidden" name="action" value="rate">
                        <div class="input-group input-group-sm mb-3">
                            <span class="input-group-text">Oceń</span>
                            <select name="rating" class="form-select">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <option value="<?= $i ?>" <?= $my_rating == $i ? 'selected' : '' ?>><?= $i ?> gwiazdek</option>
                                <?php endfor; ?>
                            </select>
                            <button class="btn btn-outline-primary" type="submit">Zapisz</button>
                        </div>
                    </form>
                <?php elseif (isLoggedIn() && $my_user_id == $photo['owner_id']): ?>
                    <p class="text-muted small mt-2">Nie możesz oceniać własnych zdjęć.</p>
                <?php elseif (!isLoggedIn()): ?>
                    <p class="text-muted small mt-2">Zaloguj się, by ocenić.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <h5 class="border-bottom pb-2">Komentarze</h5>
        <div class="mb-4" style="max-height: 400px; overflow-y: auto;">
            <?php foreach($comments as $c): ?>
            <div class="bg-body-tertiary p-2 mb-2 rounded border-start border-3 border-primary">
                <strong><?= htmlspecialchars($c['username']) ?></strong> <small class="text-muted"><?= $c['created_at'] ?></small>
                <p class="mb-0 mt-1"><?= nl2br($c['content']) ?></p>
            </div>
            <?php endforeach; ?>
            <?php if(empty($comments)): ?>
                <p class="text-muted">Brak komentarzy. Bądź pierwszy!</p>
            <?php endif; ?>
        </div>
        
        <?php if (isLoggedIn()): ?>
        <form method="post">
            <input type="hidden" name="action" value="comment">
            <div class="mb-2">
                <textarea name="content" class="form-control" rows="2" placeholder="Twój komentarz..." required></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Wyślij komentarz</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
