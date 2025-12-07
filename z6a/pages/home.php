<?php require_once 'database/database_z6a.php';

// Pobieramy piosenki z nazwą gatunku
$sql = "SELECT s.*, m.name as genre FROM song s 
        JOIN musictype m ON s.idmt = m.idmt 
        ORDER BY s.datetime DESC";
$stmt = $pdo->query($sql);
$songs = $stmt->fetchAll();
?>

<h1 class="text-3xl font-bold mb-6">Ostatnio dodane</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach($songs as $song): ?>
        <div class="bg-gray-800 p-4 rounded-lg hover:bg-gray-750 transition group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-md flex items-center justify-center">
                        <i class="bi bi-music-note-beamed text-xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg truncate w-40"><?= htmlspecialchars($song['title']) ?></h3>
                        <p class="text-gray-400 text-sm"><?= htmlspecialchars($song['musician']) ?></p>
                    </div>
                </div>
                <span class="text-xs bg-gray-700 px-2 py-1 rounded text-gray-300"><?= $song['genre'] ?></span>
            </div>

            <audio controls class="w-full h-8 mt-2 opacity-80 group-hover:opacity-100 transition">
                <source src="media/music/<?= htmlspecialchars($song['filename']) ?>" type="audio/mpeg">
                Twoja przeglądarka nie wspiera elementu audio.
            </audio>
        </div>
    <?php endforeach; ?>
</div>