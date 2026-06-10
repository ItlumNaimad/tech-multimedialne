<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    $image_id = trim($_POST['image_id'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    
    if (!empty($image_id) && !empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO gallery_comments (user_id, image_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([getCurrentUserId(), $image_id, $comment]);
    }
    header("Location: gallery.php");
    exit;
}

// Pobieranie komentarzy
$stmt = $pdo->query("SELECT c.*, u.username FROM gallery_comments c JOIN users u ON c.user_id = u.id ORDER BY c.created_at ASC");
$all_comments = $stmt->fetchAll();

// Grupowanie komentarzy po image_id
$commentsByImage = [];
foreach ($all_comments as $c) {
    $commentsByImage[$c['image_id']][] = $c;
}

// Fikcyjna galeria obrazków (zastępstwo bazy)
$images = [
    ['id' => 'img1', 'url' => 'https://picsum.photos/seed/z19_1/600/400', 'title' => 'Mistrzowskie patrzenie w sufit'],
    ['id' => 'img2', 'url' => 'https://picsum.photos/seed/z19_2/600/400', 'title' => 'Szkolenie MŚP - Część 1'],
    ['id' => 'img3', 'url' => 'https://picsum.photos/seed/z19_3/600/400', 'title' => 'Analiza leżenia bykiem w plenerze'],
];

require_once 'header.php';
?>

<div class="row">
    <div class="col-md-12 text-center mb-4">
        <h3 class="text-warning"><i class="bi bi-images"></i> Galeria z komentarzami</h3>
        <p class="text-muted">Kliknij miniaturkę aby powiększyć i skomentować. Przetestuj działanie na telefonie (jest responsywna)!</p>
    </div>
</div>

<div class="row">
    <?php foreach($images as $img): ?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <!-- Miniaturka otwierająca Modal -->
            <img src="<?= $img['url'] ?>" class="card-img-top" alt="<?= $img['title'] ?>" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modal-<?= $img['id'] ?>">
            <div class="card-body">
                <h5 class="card-title text-center"><?= $img['title'] ?></h5>
                <div class="text-center mt-3">
                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modal-<?= $img['id'] ?>">
                        <i class="bi bi-chat-text"></i> Komentarze (<?= isset($commentsByImage[$img['id']]) ? count($commentsByImage[$img['id']]) : 0 ?>)
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal (Powiększone zdjęcie + Komentarze) -->
    <div class="modal fade" id="modal-<?= $img['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><?= $img['title'] ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <img src="<?= $img['url'] ?>" class="img-fluid w-100" alt="<?= $img['title'] ?>">
                </div>
                <div class="modal-body bg-light">
                    <h6>Komentarze użytkowników:</h6>
                    <div class="list-group mb-3">
                        <?php if(!isset($commentsByImage[$img['id']])): ?>
                            <div class="list-group-item text-muted small">Brak komentarzy. Dodaj pierwszy!</div>
                        <?php else: ?>
                            <?php foreach($commentsByImage[$img['id']] as $c): ?>
                            <div class="list-group-item">
                                <strong><i class="bi bi-person"></i> <?= htmlspecialchars($c['username']) ?></strong> <span class="text-muted small">(<?= date('d.m H:i', strtotime($c['created_at'])) ?>)</span><br>
                                <?= nl2br(htmlspecialchars($c['comment'])) ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <form method="post">
                        <input type="hidden" name="action" value="add_comment">
                        <input type="hidden" name="image_id" value="<?= $img['id'] ?>">
                        <div class="input-group">
                            <input type="text" name="comment" class="form-control" placeholder="Napisz komentarz..." required>
                            <button class="btn btn-warning" type="submit"><i class="bi bi-send"></i> Wyślij</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>
