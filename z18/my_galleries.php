<?php
require_once 'db_connect.php';
require_once 'header.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = getCurrentUserId();
$username = getCurrentUsername();

// Tworzenie galerii
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'create') {
    $title = trim($_POST['title']);
    $visibility = $_POST['visibility'];
    
    if (!empty($title)) {
        // Znajdźmy najwyższy folder_name użytkownika żeby dać kolejny numer
        $stmt = $pdo->prepare("SELECT MAX(folder_name) as max_folder FROM galleries WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();
        $next_folder = ($row['max_folder'] ?? 0) + 1;
        
        $stmt = $pdo->prepare("INSERT INTO galleries (user_id, title, visibility, folder_name) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$user_id, $title, $visibility, $next_folder])) {
            $galleryPath = __DIR__ . '/uploads/' . $username . '/' . $next_folder;
            if (!is_dir($galleryPath)) {
                mkdir($galleryPath, 0777, true);
            }
        }
        header("Location: my_galleries.php");
        exit;
    }
}

// Usuwanie galerii
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $g_id = $_POST['gallery_id'];
    $stmt = $pdo->prepare("SELECT folder_name FROM galleries WHERE id = ? AND user_id = ?");
    $stmt->execute([$g_id, $user_id]);
    $g = $stmt->fetch();
    
    if ($g) {
        $pdo->prepare("DELETE FROM galleries WHERE id = ?")->execute([$g_id]);
        // Fizyczne usunięcie pików
        $dir = __DIR__ . '/uploads/' . $username . '/' . $g['folder_name'];
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.','..')); 
            foreach ($files as $file) { 
                unlink("$dir/$file"); 
            } 
            rmdir($dir); 
        }
    }
    header("Location: my_galleries.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM galleries WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$galleries = $stmt->fetchAll();
?>

<h2>Moje Galerie</h2>
<hr>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Stwórz nową galerię</h5>
                <form method="post">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label>Tytuł (np. Portrety, Rzeźby)</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Widoczność</label>
                        <select name="visibility" class="form-select">
                            <option value="public">Publiczna (widoczna dla każdego)</option>
                            <option value="private">Prywatna (tylko Ty ją widzisz)</option>
                            <option value="commercial">Komercyjna (ze znakiem wodnym)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-folder-plus"></i> Utwórz galerię</button>
                </form>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="upload.php" class="btn btn-success w-100 p-3 fs-5"><i class="bi bi-cloud-arrow-up"></i> Dodaj zdjęcie do wybranej galerii</a>
        </div>
    </div>
    
    <div class="col-md-8">
        <h4>Twoje zdefiniowane portfolio</h4>
        <ul class="list-group">
            <?php foreach($galleries as $g): ?>
                <?php
                    $vis_badge = '';
                    if($g['visibility'] == 'private') $vis_badge = '<span class="badge bg-secondary">Prywatna</span>';
                    if($g['visibility'] == 'public') $vis_badge = '<span class="badge bg-success">Publiczna</span>';
                    if($g['visibility'] == 'commercial') $vis_badge = '<span class="badge bg-warning text-dark">Komercyjna</span>';
                ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">
                            <a href="gallery.php?id=<?= $g['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($g['title']) ?></a>
                            <?= $vis_badge ?>
                        </div>
                        <small class="text-muted">Folder: /uploads/<?= $username ?>/<?= $g['folder_name'] ?></small>
                    </div>
                    <form method="post" m-0 onsubmit="return confirm('Spowoduje to usunięcie galerii oraz wszystkich zdjęć w niej zawartych. Kontynuować?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="gallery_id" value="<?= $g['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Usuń</button>
                    </form>
                </li>
            <?php endforeach; ?>
            <?php if(empty($galleries)): ?>
                <li class="list-group-item text-muted">Brak galerii. Utwórz swoją pierwszą po lewej!</li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php require_once 'footer.php'; ?>
