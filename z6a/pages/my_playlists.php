<?php require_once 'database/database.php';

// 1. Jeśli wybrano konkretną playlistę -> Pokaż jej utwory
if (isset($_GET['idpl'])) {
    $idpl = $_GET['idpl'];
    $idu = $_SESSION['user_id'] ?? null;

    // Pobierz dane playlisty
    $stmt = $pdo->prepare("SELECT * FROM playlistname WHERE idpl = ?");
    $stmt->execute([$idpl]);
    $playlist = $stmt->fetch();

    // Sprawdź, czy playlista istnieje i czy użytkownik ma do niej dostęp (jest właścicielem lub jest publiczna)
    if (!$playlist || (!$playlist['public'] && $playlist['idu'] != $idu)) {
        echo '<h3>Błąd</h3><p>Playlista nie istnieje lub nie masz do niej dostępu.</p>';
        echo '<a href="index.php?page=my_playlists" class="btn btn-secondary mb-3">&laquo; Wróć do listy</a>';
    } else {
        // Pobierz utwory w playliście
        $sql = "SELECT s.* FROM song s 
                JOIN playlistdatabase pd ON s.ids = pd.ids 
                WHERE pd.idpl = ?";
        $stmtSongs = $pdo->prepare($sql);
        $stmtSongs->execute([$idpl]);
        $songs = $stmtSongs->fetchAll();

        // --- WIDOK KONKRETNEJ PLAYLISTY ---
        echo '<h3>Playlista: ' . htmlspecialchars($playlist['name']) . '</h3>';
        echo '<a href="index.php?page=my_playlists" class="btn btn-secondary mb-3">&laquo; Wróć do listy</a>';

        if (count($songs) == 0) {
            echo '<p>Ta playlista jest pusta.</p>';
        } else {
            echo '<div class="list-group">';
            foreach($songs as $song) {
                echo '<div class="list-group-item bg-dark text-white border-secondary mb-2 d-flex align-items-center">';
                // POPRAWIONA ŚCIEŻKA do media/music/
                echo '<button class="btn btn-outline-success rounded-circle play-btn p-3 me-3" 
                              data-src="media/music/' . htmlspecialchars($song['filename']) . '"
                              data-title="' . htmlspecialchars($song['title']) . '"
                              data-artist="' . htmlspecialchars($song['musician']) . '">
                          <i class="bi bi-play-fill fs-4"></i>
                      </button>';
                echo '<div><h5>' . htmlspecialchars($song['title']) . '</h5><p class="mb-0 small">' . htmlspecialchars($song['musician']) . '</p></div>';
                echo '</div>';
            }
            echo '</div>';
        }
    }

} else {
    // 2. Jeśli nie wybrano -> Pokaż listę playlist (własnych i publicznych)
    $idu = $_SESSION['user_id'];

    $sql = "SELECT p.*, u.username 
            FROM playlistname p
            JOIN user u ON p.idu = u.idu
            WHERE p.idu = ? OR p.public = 1 
            ORDER BY p.datetime DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idu]);
    $playlists = $stmt->fetchAll();

    echo '<h2>Wszystkie Playlisty</h2>';
    echo '<a href="index.php?page=create_playlist" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> Nowa Playlista</a>';

    if (count($playlists) == 0) {
        echo '<p>Nie ma jeszcze żadnych playlist.</p>';
    } else {
        echo '<div class="list-group">';
        foreach($playlists as $pl) {
            $is_owner = ($pl['idu'] == $idu);

            echo '<a href="index.php?page=my_playlists&idpl=' . $pl['idpl'] . '" class="list-group-item list-group-item-action bg-dark text-white border-secondary d-flex justify-content-between align-items-center">';
            echo '<div><h5 class="mb-1">' . htmlspecialchars($pl['name']) . '</h5>';

            if ($is_owner) {
                echo '<small>' . ($pl['public'] ? 'Publiczna 🌍' : 'Prywatna 🔒') . '</small>';
            } else {
                echo '<small>Publiczna 🌍 od: ' . htmlspecialchars($pl['username']) . '</small>';
            }
            
            echo '</div><i class="bi bi-chevron-right"></i></a>';
        }
        echo '</div>';
    }
}
?>