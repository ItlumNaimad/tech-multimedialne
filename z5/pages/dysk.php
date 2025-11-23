<?php
// Pobieramy aktualną ścieżkę wewnątrz katalogu usera (np. "wakacje/2023")
$current_path = $_GET['path'] ?? '';
// Zabezpieczenie przed wyjściem "wyżej" (..)
$current_path = str_replace('..', '', $current_path);

// Pełna ścieżka fizyczna na serwerze
// Odwołujemy się do folderu mycloud_files w public_html
// (Skoro index.php jest w z5/, musimy wyjść raz do góry do public_html)
$user_root_dir = '../mycloud_files/' . $_SESSION['username'] . '/';
$scan_dir = $user_root_dir . $current_path;

// Upewnij się, że nie ma podwójnych slashy
$scan_dir = preg_replace('#/+#','/',$scan_dir);

// Sprawdź czy katalog istnieje, jak nie to wróć do głównego
if (!is_dir($scan_dir)) {
    $scan_dir = $user_root_dir;
    $current_path = '';
}

// Pobierz listę plików i folderów (pomijając . i ..)
$items = array_diff(scandir($scan_dir), array('.', '..'));
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-folder2-open"></i> Mój Dysk: /<?php echo htmlspecialchars($current_path); ?></h3>

        <?php if ($current_path !== ''):
            $parent_path = dirname($current_path);
            if ($parent_path === '.') $parent_path = '';
            ?>
            <a href="index.php?page=home&path=<?php echo urlencode($parent_path); ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-up-circle"></i> W górę
            </a>
        <?php endif; ?>
    </div>

    <div class="row mb-4 p-3 bg-light border rounded">

        <div class="col-md-6">
            <form action="scripts/upload.php" method="post" enctype="multipart/form-data" class="d-flex gap-2">
                <input type="hidden" name="current_path" value="<?php echo htmlspecialchars($current_path); ?>">
                <input class="form-control" type="file" name="fileToUpload" required>
                <button type="submit" class="btn btn-success"><i class="bi bi-cloud-upload"></i> Wyślij</button>
            </form>
        </div>

        <div class="col-md-6">
            <form action="scripts/mkdir.php" method="post" class="d-flex gap-2">
                <input type="hidden" name="current_path" value="<?php echo htmlspecialchars($current_path); ?>">
                <input type="text" class="form-control" name="folder_name" placeholder="Nazwa nowego folderu" required>
                <button type="submit" class="btn btn-primary"><i class="bi bi-folder-plus"></i> Stwórz</button>
            </form>
        </div>
    </div>

    <div class="list-group">
        <?php if (count($items) > 0): ?>
            <?php foreach ($items as $item):
                $item_path = $scan_dir . '/' . $item;
                $is_dir = is_dir($item_path);
                $item_url_path = ($current_path ? $current_path . '/' : '') . $item;
                $icon = $is_dir ? 'bi-folder-fill text-warning' : 'bi-file-earmark-text text-primary';
                ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi <?php echo $icon; ?> fs-4 me-3"></i>

                        <?php if ($is_dir): ?>
                            <a href="index.php?page=home&path=<?php echo urlencode($item_url_path); ?>" class="text-decoration-none fw-bold text-dark">
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo '../mycloud_files/' . $_SESSION['username'] . '/' . $item_url_path; ?>" target="_blank" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="btn-group">
                        <?php if (!$is_dir): ?>
                            <a href="scripts/download.php?file=<?php echo urlencode($item); ?>&path=<?php echo urlencode($current_path); ?>" class="btn btn-sm btn-outline-primary" title="Pobierz">
                                <i class="bi bi-download"></i>
                            </a>
                        <?php endif; ?>

                        <a href="scripts/delete.php?file=<?php echo urlencode($item); ?>&path=<?php echo urlencode($current_path); ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Czy na pewno chcesz usunąć?');" title="Usuń">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">Ten folder jest pusty.</div>
        <?php endif; ?>
    </div>
</div>