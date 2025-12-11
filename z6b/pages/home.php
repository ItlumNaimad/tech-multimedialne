<?php require_once 'database/database.php';

// Pobieramy filmy
$sql = "SELECT f.*, ft.name as genre FROM film f 
        JOIN filmtype ft ON f.idft = ft.idft 
        ORDER BY f.datetime DESC";
$stmt = $pdo->query($sql);
$films = $stmt->fetchAll();
?>

<h2 class="mb-4">Biblioteka Filmów</h2>

<div class="row">
    <?php foreach($films as $film): ?>
        <div class="col-md-6 mb-4"> <div class="card bg-secondary text-white h-100">
                <div class="ratio ratio-16x9">
                    <video controls class="card-img-top">
                        <source src="media/films/<?= htmlspecialchars($film['filename']) ?>" type="video/mp4">
                        Twoja przeglądarka nie wspiera tagu video.
                    </video>
                </div>
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title"><?= htmlspecialchars($film['title']) ?></h5>
                            <p class="card-text text-light small mb-0">
                                <i class="bi bi-camera-reels"></i> Reż: <?= htmlspecialchars($film['director']) ?>
                            </p>
                            <span class="badge bg-dark mt-2"><?= $film['genre'] ?></span>
                        </div>
                        
                        <a href="index.php?page=add_to_playlist&film_id=<?= $film['idf'] ?>" class="btn btn-sm btn-outline-light" title="Dodaj do playlisty">
                            <i class="bi bi-plus-lg"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>