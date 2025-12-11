<?php require_once 'database/database.php';

$film_id = $_GET['film_id'] ?? null;
if (!$film_id) die("Nie wybrano filmu.");

// Pobieramy playlisty ZALOGOWANEGO użytkownika
$idu = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM playlistname WHERE idu = ?");
$stmt->execute([$idu]);
$playlists = $stmt->fetchAll();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark text-white">
            <div class="card-header">Dodaj film do playlisty</div>
            <div class="card-body">
                <form action="scripts/add_film_handler.php" method="POST">
                    <input type="hidden" name="film_id" value="<?= htmlspecialchars($film_id) ?>">

                    <div class="mb-3">
                        <label class="form-label">Wybierz playlistę:</label>
                        <select name="playlist_id" class="form-select" required>
                            <?php foreach($playlists as $pl): ?>
                                <option value="<?= $pl['idpl'] ?>">
                                    <?= htmlspecialchars($pl['name']) ?>
                                    (<?= $pl['public'] ? 'Publiczna' : 'Prywatna' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if(count($playlists) > 0): ?>
                        <button type="submit" class="btn btn-primary w-100">Dodaj</button>
                    <?php else: ?>
                        <div class="alert alert-warning">Nie masz jeszcze żadnych playlist. <a href="index.php?page=create_playlist">Stwórz nową.</a></div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>