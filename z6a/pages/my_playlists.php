<?php require_once 'database/database.php';

// 1. Jeśli wybrano konkretną playlistę -> Pokaż jej utwory
if (isset($_GET['idpl'])) {
    $idpl = $_GET['idpl'];

    // Pobierz nazwę playlisty
    $stmt = $pdo->prepare("SELECT * FROM playlistname WHERE idpl = ?");
    $stmt->execute([$idpl]);
    $playlist = $stmt->fetch();

    // Pobierz utwory w playliście (JOIN tabela łącząca z tabelą piosenek)
    $sql = "SELECT s.* FROM song s 
            JOIN playlistdatabase pd ON s.ids = pd.ids 
            WHERE pd.idpl = ?";
    $stmtSongs = $pdo->prepare($sql);
    $stmtSongs->execute([$idpl]);
    $songs = $stmtSongs->fetchAll();

    // --- WIDOK KONKRETNEJ PLAYLISTY ---
    echo '<h3>Playlista: ' . htmlspecialchars($playlist['name']) . '</h3>';
    echo '<a href="index.php?page=my_playlists" class="btn btn-secondary mb-3">&laquo; Wróć do listy</a>';

    echo '<div class="list-group">';
    foreach($songs as $song) {
        echo '<div class="list-group-item bg-dark text-white border-secondary mb-2 d-flex align-items-center">';
        
        // Nowy przycisk Play
        echo '<button class="btn btn-outline-success rounded-circle play-btn p-3 me-3" 
                      data-src="media/music/' . htmlspecialchars($song['filename']) . '"
                      data-title="' . htmlspecialchars($song['title']) . '"
                      data-artist="' . htmlspecialchars($song['musician']) . '">
                  <i class="bi bi-play-fill fs-4"></i>
              </button>';
        
        // Tytuł i wykonawca
        echo '<div>';
        echo '<h5>' . htmlspecialchars($song['title']) . '</h5>';
        echo '<p class="mb-0 small">' . htmlspecialchars($song['musician']) . '</p>';
        echo '</div>';

        echo '</div>';
    }
    if (count($songs) == 0) echo '<p>Pusta playlista.</p>';
    echo '</div>';

} else {
    // 2. Jeśli nie wybrano -> Pokaż listę playlist użytkownika
    $idu = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT * FROM playlistname WHERE idu = ? ORDER BY datetime DESC");
    $stmt->execute([$idu]);
    $playlists = $stmt->fetchAll();

    echo '<h2>Moje Playlisty</h2>';
    echo '<a href="index.php?page=create_playlist" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> Nowa Playlista</a>';

    echo '<div class="list-group">';
    foreach($playlists as $pl) {
        echo '<a href="index.php?page=my_playlists&idpl=' . $pl['idpl'] . '" class="list-group-item list-group-item-action bg-dark text-white border-secondary d-flex justify-content-between align-items-center">';
        echo '<div><h5 class="mb-1">' . htmlspecialchars($pl['name']) . '</h5>';
        echo '<small>' . ($pl['public'] ? 'Publiczna 🌍' : 'Prywatna 🔒') . '</small></div>';
        echo '<i class="bi bi-chevron-right"></i>';
        echo '</a>';
    }
    echo '</div>';
}
?>