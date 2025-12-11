<?php require_once 'database/database.php';

// 1. Jeśli wybrano konkretną playlistę -> Pokaż jej filmy
if (isset($_GET['idpl'])) {
    $idpl = $_GET['idpl'];
    $idu = $_SESSION['user_id'] ?? null;

    // Pobierz dane playlisty
    $stmt = $pdo->prepare("SELECT * FROM playlistname WHERE idpl = ?");
    $stmt->execute([$idpl]);
    $playlist = $stmt->fetch();

    // Sprawdź, czy playlista istnieje i czy użytkownik ma do niej dostęp
    if (!$playlist || (!$playlist['public'] && $playlist['idu'] != $idu)) {
        echo '<h3>Błąd</h3><p>Playlista nie istnieje lub nie masz do niej dostępu.</p>';
        echo '<a href="index.php?page=my_playlists" class="btn btn-secondary mb-3">&laquo; Wróć do listy</a>';
    } else {
        // Pobierz filmy w playliście
        $sql = "SELECT f.* FROM film f 
                JOIN playlistdatabase pd ON f.idf = pd.idf 
                WHERE pd.idpl = ?";
        $stmtFilms = $pdo->prepare($sql);
        $stmtFilms->execute([$idpl]);
        $films = $stmtFilms->fetchAll();

        // --- WIDOK KONKRETNEJ PLAYLISTY ---
        echo '<h3>Playlista: ' . htmlspecialchars($playlist['name']) . '</h3>';
        echo '<a href="index.php?page=my_playlists" class="btn btn-secondary mb-3">&laquo; Wróć do listy</a>';

        if (count($films) == 0) {
            echo '<p>Ta playlista jest pusta.</p>';
        } else {
            echo '<div class="row">';
            foreach($films as $film) {
                echo '<div class="col-md-6 col-lg-4 mb-4">';
                echo '  <div class="card bg-dark text-white">';
                echo '    <video controls width="100%"><source src="media/films/' . htmlspecialchars($film['filename']) . '" type="video/mp4"></video>';
                echo '    <div class="card-body">';
                echo '      <h5 class="card-title">' . htmlspecialchars($film['title']) . '</h5>';
                echo '      <p class="card-text small">Reż: ' . htmlspecialchars($film['director']) . '</p>';
                echo '    </div>';
                echo '  </div>';
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
            JOIN users u ON p.idu = u.id 
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