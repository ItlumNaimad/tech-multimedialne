<?php require_once 'database/database.php';

// ZMIANA: Pobieramy film_id zamiast song_id
$film_id = $_GET['film_id'] ?? null;
if (!$film_id) die("Nie wybrano filmu.");

$idu = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM playlistname WHERE idu = ?");
$stmt->execute([$idu]);
$playlists = $stmt->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark text-white border-danger">
            <div class="card-header border-danger">Dodaj film do playlisty</div>
            <div class="card-body">

                <form action="scripts/add_film_handler.php" method="POST">

                    <input type="hidden" name="film_id" value="<?= htmlspecialchars($film_id) ?>">

                    <div class="mb-3">
                        <label class="form-label">Wybierz playlistę:</label>
                        <select name="playlist_id" class="form-select bg-secondary text-white border-0" required>
                            <?php foreach($playlists as $pl): ?>
                                <option value="<?= $pl['idpl'] ?>">
                                    <?= htmlspecialchars($pl['name']) ?>
                                    (<?= $pl['public'] ? 'Publiczna' : 'Prywatna' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-danger w-100">Dodaj</button>
                </form>
            </div>
        </div>
    </div>
</div>