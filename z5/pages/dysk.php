<?php
// Logika pobierania ścieżek (bez zmian)
$current_path = $_GET['path'] ?? '';
$current_path = str_replace('..', '', $current_path);

$user_root_dir = '../mycloud_files/' . $_SESSION['username'] . '/';
$scan_dir = $user_root_dir . $current_path;
$scan_dir = preg_replace('#/+#','/',$scan_dir);

if (!is_dir($scan_dir)) {
    $scan_dir = $user_root_dir;
    $current_path = '';
}

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

                // Ścieżka URL do pliku (do wyświetlenia w przeglądarce)
                $web_path = '../mycloud_files/' . $_SESSION['username'] . '/' . $item_url_path;

                // --- NOWA LOGIKA: Sprawdzanie typu pliku ---
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                $is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $is_pdf = ($ext === 'pdf');

                // Ikony
                if ($is_dir) {
                    $icon = 'bi-folder-fill text-warning';
                } elseif ($is_image) {
                    $icon = 'bi-file-earmark-image text-success';
                } elseif ($is_pdf) {
                    $icon = 'bi-file-earmark-pdf text-danger';
                } else {
                    $icon = 'bi-file-earmark-text text-primary';
                }
                ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center text-truncate">
                        <i class="bi <?php echo $icon; ?> fs-4 me-3"></i>

                        <?php if ($is_dir): ?>
                            <a href="index.php?page=home&path=<?php echo urlencode($item_url_path); ?>" class="text-decoration-none fw-bold text-dark">
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                        <?php elseif ($is_image || $is_pdf): ?>
                            <a href="#"
                               class="text-decoration-none text-dark preview-trigger"
                               data-bs-toggle="modal"
                               data-bs-target="#previewModal"
                               data-file-type="<?php echo $is_pdf ? 'pdf' : 'image'; ?>"
                               data-file-url="<?php echo $web_path; ?>"
                               data-file-name="<?php echo htmlspecialchars($item); ?>">
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                        <?php else: ?>
                            <a href="<?php echo $web_path; ?>" target="_blank" class="text-decoration-none text-dark">
                                <?php echo htmlspecialchars($item); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="btn-group">
                        <?php if (!$is_dir): ?>
                            <a href="scripts/download.php?file=<?php echo urlencode($item); ?>&path=<?php echo urlencode($current_path); ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-download"></i>
                            </a>
                        <?php endif; ?>

                        <a href="scripts/delete.php?file=<?php echo urlencode($item); ?>&path=<?php echo urlencode($current_path); ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Czy na pewno chcesz usunąć?');">
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

<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered"> <div class="modal-content" style="height: 90vh;"> <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Podgląd pliku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex justify-content-center align-items-center bg-light" id="previewBody">
            </div>
        </div>
    </div>
</div>

<script>
    // Pobieramy element modala
    var previewModal = document.getElementById('previewModal');

    // Nasłuchujemy zdarzenia "show.bs.modal" (gdy modal zaczyna się otwierać)
    previewModal.addEventListener('show.bs.modal', function (event) {
        // Przycisk, który uruchomił modal
        var button = event.relatedTarget;

        // Pobieramy dane z atrybutów data-*
        var fileUrl = button.getAttribute('data-file-url');
        var fileType = button.getAttribute('data-file-type');
        var fileName = button.getAttribute('data-file-name');

        // Aktualizujemy tytuł
        var modalTitle = previewModal.querySelector('.modal-title');
        modalTitle.textContent = fileName;

        // Aktualizujemy treść (Body)
        var modalBody = previewModal.querySelector('.modal-body');
        modalBody.innerHTML = ''; // Czyścimy poprzednią treść

        if (fileType === 'pdf') {
            // Dla PDF wstawiamy IFRAME
            var iframe = document.createElement('iframe');
            iframe.src = fileUrl;
            iframe.width = "100%";
            iframe.height = "100%";
            iframe.style.border = "none";
            modalBody.appendChild(iframe);
        } else if (fileType === 'image') {
            // Dla Obrazka wstawiamy IMG
            var img = document.createElement('img');
            img.src = fileUrl;
            img.style.maxWidth = "100%";
            img.style.maxHeight = "100%";
            img.style.objectFit = "contain"; // Zachowaj proporcje
            modalBody.appendChild(img);
        }
    });

    // Opcjonalnie: Czyścimy modal po zamknięciu (żeby zatrzymać wideo/audio itp.)
    previewModal.addEventListener('hidden.bs.modal', function () {
        var modalBody = previewModal.querySelector('.modal-body');
        modalBody.innerHTML = '';
    });
</script>