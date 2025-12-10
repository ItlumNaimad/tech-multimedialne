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
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-outline-success rounded-circle play-btn p-3 me-3" 
                                    data-src="media/music/<?= htmlspecialchars($song['filename']) ?>"
                                    data-title="<?= htmlspecialchars($song['title']) ?>"
                                    data-artist="<?= htmlspecialchars($song['musician']) ?>">
                                <i class="bi bi-play-fill fs-4"></i>
                            </button>
                            <div>
                                <h5 class="card-title mb-0"><?= htmlspecialchars($song['title']) ?></h5>
                                <p class="card-text text-light small mb-0">
                                    <i class="bi bi-mic"></i> <?= htmlspecialchars($song['musician']) ?>
                                </p>
                            </div>
                        </div>
                        <a href="index.php?page=add_to_playlist&song_id=<?= $song['ids'] ?>" class="btn btn-sm btn-outline-light" title="Dodaj do playlisty">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    </div>
                    <div class="mt-auto">
                         <span class="badge bg-dark"><?= $song['genre'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
