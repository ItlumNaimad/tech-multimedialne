<?php require_once 'database/database.php';
// Pobieramy gatunki
$stmt = $pdo->query("SELECT * FROM musictype");
$types = $stmt->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-secondary text-white">
            <div class="card-header">Dodaj nową piosenkę</div>
            <div class="card-body">
                <form action="scripts/upload_music.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Tytuł utworu</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Wykonawca</label>
                        <input type="text" name="musician" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gatunek</label>
                        <select name="idmt" class="form-select">
                            <?php foreach($types as $type): ?>
                                <option value="<?= $type['idmt'] ?>"><?= $type['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plik MP3</label>
                        <input type="file" name="music_file" accept=".mp3" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Wrzuć na serwer</button>
                </form>
            </div>
        </div>
    </div>
</div>