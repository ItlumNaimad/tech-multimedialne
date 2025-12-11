<?php require_once 'database/database.php';
// Pobieramy gatunki
$stmt = $pdo->query("SELECT * FROM filmtype");
$types = $stmt->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-secondary text-white">
            <div class="card-header">Dodaj nową piosenkę</div>
            <div class="card-body">
                <form action="scripts/upload_films.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Tytuł filmu</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reżyer</label>
                        <input type="text" name="director" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gatunek</label>
                        <select name="idft" class="form-select">
                            <?php foreach($types as $type): ?>
                                <option value="<?= $type['idft'] ?>"><?= $type['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Plik MP4</label>
                        <input type="file" name="film_file" accept=".mp4" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Wrzuć na serwer</button>
                </form>
            </div>
        </div>
    </div>
</div>