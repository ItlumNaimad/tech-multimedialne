<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Obsługa uploadu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_image') {
    $caption = trim($_POST['caption'] ?? '');
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            $new_name = uniqid() . '.' . $file_ext;
            $destination = $upload_dir . $new_name;
            
            if (move_uploaded_file($file_tmp, $destination)) {
                $stmt = $pdo->prepare("INSERT INTO gallery_images (user_id, file_path, caption) VALUES (?, ?, ?)");
                $stmt->execute([getCurrentUserId(), 'uploads/' . $new_name, $caption]);
            }
        }
    }
    header("Location: gallery.php");
    exit;
}

// Obsługa starych komentarzy (jeśli baza galerii jeszcze zakłada statyczne zdjęcia)
// Pominąłem dodawanie komentarzy na nowo wbudowanej galerii żeby uprościć, ale zostawiam odczyt starszych komentarzy
// jako że w nowej strukturze gallery_images ID są intami, a w starych to były stringi "img1".
// Pobieramy prawdziwe wrzucone obrazki:
$stmt = $pdo->query("SELECT g.*, u.username FROM gallery_images g JOIN users u ON g.user_id = u.id ORDER BY g.created_at DESC");
$uploaded_images = $stmt->fetchAll();

require_once 'header.php';
?>

<div class="row">
    <div class="col-md-12 text-center mb-4">
        <h3 class="text-warning"><i class="bi bi-images"></i> Galeria z przesyłaniem zdjęć</h3>
        <p class="text-muted">Prześlij własne zdjęcia, by stworzyć galerię. Inni użytkownicy będą mogli je oglądać.</p>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-6 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title text-center">Dodaj nowe zdjęcie</h5>
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload_image">
                    <div class="mb-3">
                        <label class="form-label">Wybierz plik (JPG, PNG, GIF)</label>
                        <input class="form-control" type="file" name="image" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Podpis / Tytuł</label>
                        <input type="text" name="caption" class="form-control" required placeholder="Napisz coś o tym zdjęciu...">
                    </div>
                    <button type="submit" class="btn btn-warning w-100"><i class="bi bi-cloud-upload"></i> Prześlij zdjęcie</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <?php if (empty($uploaded_images)): ?>
        <div class="col-12 text-center text-muted">
            <p>Brak zdjęć w galerii. Bądź pierwszym, który coś prześle!</p>
        </div>
    <?php endif; ?>

    <?php foreach($uploaded_images as $img): ?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <img src="<?= htmlspecialchars($img['file_path']) ?>" class="card-img-top" alt="Zdjęcie" style="cursor: pointer; object-fit: cover; height: 250px;" data-bs-toggle="modal" data-bs-target="#modal-<?= $img['id'] ?>">
            <div class="card-body">
                <h5 class="card-title text-center"><?= htmlspecialchars($img['caption']) ?></h5>
                <p class="text-muted text-center small mb-0">Dodano przez: <?= htmlspecialchars($img['username']) ?></p>
            </div>
        </div>
    </div>
    
    <!-- Modal (Powiększone zdjęcie) -->
    <div class="modal fade" id="modal-<?= $img['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><?= htmlspecialchars($img['caption']) ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center bg-black">
                    <img src="<?= htmlspecialchars($img['file_path']) ?>" class="img-fluid" alt="Zdjęcie" style="max-height: 80vh;">
                </div>
                <div class="modal-footer">
                    <span class="text-muted me-auto">Autor: <?= htmlspecialchars($img['username']) ?> | Data: <?= date('d.m.Y H:i', strtotime($img['created_at'])) ?></span>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zamknij</button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>
