<?php
require_once 'db_connect.php';
require_once 'header.php';
require_once 'ImageHelper.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = getCurrentUserId();
$username = getCurrentUsername();

$stmt = $pdo->prepare("SELECT * FROM galleries WHERE user_id = ? ORDER BY title ASC");
$stmt->execute([$user_id]);
$galleries = $stmt->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['photo'])) {
    $gallery_id = $_POST['gallery_id'] ?? null;
    $title = trim($_POST['title']);
    $filter = $_POST['filter'] ?? 'none';
    
    if (!$gallery_id || empty($title) || $_FILES['photo']['error'] != UPLOAD_ERR_OK) {
        $error = "Proszę uzupełnić galerię, zapodać tytuł i poprawne zdjęcie.";
    } else {
        // Zdobądź folder galerii by sprawdzić uprawnienia
        $stmt = $pdo->prepare("SELECT folder_name, visibility FROM galleries WHERE id = ? AND user_id = ?");
        $stmt->execute([$gallery_id, $user_id]);
        $gallery = $stmt->fetch();
        
        if (!$gallery) {
            $error = "Nie odnaleziono galerii lub nie masz uprawnień.";
        } else {
            $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (!in_array($ext, $allowed)) {
                $error = "Niedozwolony format pliku. Użyj JPG lub PNG.";
            } else {
                $new_filename = time() . '_' . uniqid() . '.' . $ext;
                $dest_dir = __DIR__ . "/uploads/$username/" . $gallery['folder_name'];
                
                if (!is_dir($dest_dir)) mkdir($dest_dir, 0777, true);
                
                $dest_path = $dest_dir . '/' . $new_filename;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest_path)) {
                    // Aplikowanie filtrów (grayscale, sepia, negatyw itp.)
                    if ($filter !== 'none') {
                        ImageHelper::applyFilter($dest_path, $filter);
                    }
                    
                    // Znak wodny dla komercyjnych
                    if ($gallery['visibility'] == 'commercial') {
                        ImageHelper::applyWatermark($dest_path);
                    }
                    
                    // Dodanie do bazy
                    $db_path = "uploads/$username/" . $gallery['folder_name'] . "/" . $new_filename;
                    $stmt = $pdo->prepare("INSERT INTO photos (gallery_id, title, filename) VALUES (?, ?, ?)");
                    $stmt->execute([$gallery_id, $title, $db_path]);
                    
                    $success = "Zdjęcie pomyślnie dodane obrobione!";
                } else {
                    $error = "Błąd przenoszenia pliku na dysku serwera.";
                }
            }
        }
    }
}
?>
<!-- Styl dedykowany dla widoku mobile -->
<style>
@media (max-width: 576px) {
    .upload-card { border: 0 !important; box-shadow: none !important; }
}
</style>

<h2>Wrzuć w sieć!</h2>
<p class="text-muted">Aplikacja Mobile/Web Upload - użyj kamery ze swojego urządzenia.</p>

<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

<?php if (empty($galleries)): ?>
    <div class="alert alert-warning">
        Nie posiadasz jeszcze żadnej galerii. <a href="my_galleries.php" class="alert-link">Przejdź tu, aby jakąś założyć.</a>
    </div>
<?php else: ?>
    <div class="card upload-card mb-5 mx-auto" style="max-width: 600px;">
        <div class="card-body p-4">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label">Zrób zdjęcie lub wybierz (Mobile friendly)</label>
                    <!-- capture="camera" jest idealne dla smartfonów -->
                    <input type="file" name="photo" class="form-control form-control-lg bg-light" accept="image/*" capture="environment" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Nazwa zdjęcia</label>
                    <input type="text" name="title" class="form-control" placeholder="Wpisz krótki tytuł" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Wybierz docelową Galerię</label>
                    <select name="gallery_id" class="form-select form-select-lg" required>
                        <?php foreach($galleries as $g): ?>
                            <option value="<?= $g['id'] ?>">
                                <?= htmlspecialchars($g['title']) ?> 
                                [<?= $g['visibility'] == 'commercial' ? 'Komercyjna - nałoży znak wodny' : ($g['visibility'] == 'public' ? 'Publiczna' : 'Prywatna') ?>]
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Opcjonalny filtr (przetworzy na żywo system GD)</label>
                    <div class="d-flex flex-wrap gap-2">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="filter" id="f_none" value="none" checked>
                          <label class="form-check-label" for="f_none">Oryginał</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="filter" id="f_gray" value="greyscale">
                          <label class="form-check-label" for="f_gray">Odcienie szarości (Czarnobiałe)</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="filter" id="f_sepia" value="sepia">
                          <label class="form-check-label" for="f_sepia">Sepia</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="filter" id="f_neg" value="negatyw">
                          <label class="form-check-label" for="f_neg">Negatyw</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill mt-2">
                    <i class="bi bi-upload"></i> Opublikuj Zdjęcie!
                </button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
