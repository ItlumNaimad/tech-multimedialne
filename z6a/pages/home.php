<?php require_once 'database/database.php';

// Pobieramy piosenki
$sql = "SELECT s.*, m.name as genre FROM song s 
        JOIN musictype m ON s.idmt = m.idmt 
        ORDER BY s.datetime DESC";
$stmt = $pdo->query($sql);
$songs = $stmt->fetchAll();
?>

<h2 class="mb-4">Biblioteka Muzyczna</h2>

<div class="row">
    <?php foreach($songs as $song): ?>
        <div class="col-md-4 mb-4">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title"><?= htmlspecialchars($song['title']) ?></h5>
                        </div>
                        <a href="index.php?page=add_to_playlist&song_id=<?= $song['ids'] ?>" class="btn btn-sm btn-outline-light" title="Dodaj do playlisty">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    </div>
                    <p class="card-text text-light small">
                        <i class="bi bi-mic"></i> <?= htmlspecialchars($song['musician']) ?> <br>
                        <span class="badge bg-dark"><?= $song['genre'] ?></span>
                    </p>

                    <audio controls class="w-100 mt-2">
                        <source src="media/music/<?= htmlspecialchars($song['filename']) ?>" type="audio/mpeg">
                        Twoja przeglądarka nie wspiera audio.
                    </audio>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>