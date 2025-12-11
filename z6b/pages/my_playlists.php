<?php require_once 'database/database.php';

// 1. Jeśli wybrano konkretną playlistę -> Pokaż jej filmy
if (isset($_GET['idpl'])) {
    $idpl = $_GET['idpl'];
    $idu = $_SESSION['user_id'] ?? null;

    // Pobierz dane playlisty
    $stmt = $pdo->prepare("SELECT * FROM playlistname WHERE idpl = ?");
    $stmt->execute([$idpl]);
    $playlist = $stmt->fetch();

    // Sprawdź uprawnienia
    if (!$playlist || (!$playlist['public'] && $playlist['idu'] != $idu)) {
        echo '<h3>Błąd</h3><p>Playlista nie istnieje lub nie masz do niej dostępu.</p>';
        echo '<a href="index.php?page=my_playlists" class="btn btn-secondary mb-3">&laquo; Wróć do listy</a>';
    } else {
        // Pobierz filmy
        $sql = "SELECT f.* FROM film f 
                JOIN playlistdatabase pd ON f.idf = pd.idf 
                WHERE pd.idpl = ?";
        $stmtFilms = $pdo->prepare($sql);
        $stmtFilms->execute([$idpl]);
        $films = $stmtFilms->fetchAll(PDO::FETCH_ASSOC);

        // Przygotuj dane dla JavaScript (tablica obiektów)
        $jsPlaylistData = [];
        foreach ($films as $f) {
            $jsPlaylistData[] = [
                'src' => 'media/films/' . $f['filename'],
                'title' => $f['title'],
                'director' => $f['director']
            ];
        }
        $jsonFilms = json_encode($jsPlaylistData);

        // --- WIDOK KONKRETNEJ PLAYLISTY ---
        echo '<h3>Playlista: ' . htmlspecialchars($playlist['name']) . '</h3>';
        echo '<a href="index.php?page=my_playlists" class="btn btn-secondary mb-3">&laquo; Wróć do listy</a>';

        if (count($films) == 0) {
            echo '<p>Ta playlista jest pusta.</p>';
        } else {
            echo '<div class="row">';
            foreach($films as $index => $film) {
                echo '<div class="col-md-6 col-lg-4 mb-4">';
                echo '  <div class="card bg-dark text-white h-100">';
                // Zamiast playera, dajemy miniaturkę/przycisk, który odpala Overlay
                echo '    <div class="ratio ratio-16x9 bg-secondary d-flex align-items-center justify-content-center" style="cursor: pointer;" onclick="openCinemaMode(' . $index . ')">';
                echo '       <i class="bi bi-play-circle-fill fs-1 text-white"></i>';
                echo '    </div>';
                echo '    <div class="card-body">';
                echo '      <h5 class="card-title">' . htmlspecialchars($film['title']) . '</h5>';
                echo '      <p class="card-text small">Reż: ' . htmlspecialchars($film['director']) . '</p>';
                echo '      <button class="btn btn-danger btn-sm w-100" onclick="openCinemaMode(' . $index . ')"><i class="bi bi-play-fill"></i> Odtwórz</button>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>

        <!-- OVERLAY PLAYER (KINO DOMOWE) -->
        <div id="cinema-overlay" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-black" style="z-index: 2000; display: flex; flex-direction: column;">
            
            <!-- Pasek górny (Tytuł i Zamknij) -->
            <div class="d-flex justify-content-between align-items-center p-3 bg-dark bg-opacity-75 text-white" style="position: absolute; top: 0; width: 100%; z-index: 2001;">
                <div>
                    <h5 id="cinema-title" class="m-0 fw-bold">Tytuł Filmu</h5>
                    <small id="cinema-director" class="text-gray-400">Reżyser</small>
                </div>
                <button class="btn btn-outline-light rounded-circle" onclick="closeCinemaMode()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <!-- Kontener Wideo -->
            <div class="flex-grow-1 d-flex align-items-center justify-content-center bg-black position-relative">
                <video id="cinema-video" controls autoplay style="max-width: 100%; max-height: 100%; width: 100%; height: auto; outline: none;">
                    Twoja przeglądarka nie wspiera tagu video.
                </video>
                
                <!-- Przyciski nawigacji (nakładane na wideo) -->
                <button class="btn btn-link text-white position-absolute start-0 top-50 translate-middle-y p-4" style="font-size: 3rem; opacity: 0.5; text-decoration: none;" onclick="playPrev()" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button class="btn btn-link text-white position-absolute end-0 top-50 translate-middle-y p-4" style="font-size: 3rem; opacity: 0.5; text-decoration: none;" onclick="playNext()" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>

        <script>
            // Przekazujemy dane z PHP do JS
            const playlist = <?= $jsonFilms ?>;
            let currentIndex = 0;
            const overlay = document.getElementById('cinema-overlay');
            const video = document.getElementById('cinema-video');
            const titleEl = document.getElementById('cinema-title');
            const directorEl = document.getElementById('cinema-director');

            function openCinemaMode(index) {
                if(index < 0 || index >= playlist.length) return;
                
                currentIndex = index;
                loadFilm(currentIndex);
                
                overlay.classList.remove('d-none');
                overlay.classList.add('d-flex');
                
                // Wejdź w tryb pełnoekranowy (opcjonalnie)
                if (document.documentElement.requestFullscreen) {
                    // document.documentElement.requestFullscreen().catch(e => {});
                }
            }

            function closeCinemaMode() {
                video.pause();
                overlay.classList.add('d-none');
                overlay.classList.remove('d-flex');
                if (document.exitFullscreen) {
                    document.exitFullscreen().catch(e => {});
                }
            }

            function loadFilm(index) {
                const film = playlist[index];
                video.src = film.src;
                titleEl.textContent = film.title;
                directorEl.textContent = film.director;
                video.play();
            }

            function playNext() {
                if (currentIndex < playlist.length - 1) {
                    currentIndex++;
                    loadFilm(currentIndex);
                } else {
                    // Koniec playlisty - zamknij lub zapętl
                    closeCinemaMode();
                }
            }

            function playPrev() {
                if (currentIndex > 0) {
                    currentIndex--;
                    loadFilm(currentIndex);
                }
            }

            // Automatyczne przejście do następnego filmu
            video.addEventListener('ended', () => {
                playNext();
            });

            // Obsługa klawiszy
            document.addEventListener('keydown', (e) => {
                if (overlay.classList.contains('d-none')) return;

                if (e.key === 'Escape') closeCinemaMode();
                if (e.key === 'ArrowRight') playNext();
                if (e.key === 'ArrowLeft') playPrev();
            });
        </script>
        <?php
    }

} else {
    // 2. Jeśli nie wybrano -> Pokaż listę playlist (własnych i publicznych)
    // (Ten kod pozostaje bez zmian, taki jak był wcześniej)
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