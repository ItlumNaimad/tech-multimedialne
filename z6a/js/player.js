document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('main-audio');
    const playerBar = document.getElementById('global-player-bar');
    const titleEl = document.getElementById('player-title');
    const artistEl = document.getElementById('player-artist');
    const playBtn = document.getElementById('btn-play-pause');
    const nextBtn = document.getElementById('btn-next');
    const prevBtn = document.getElementById('btn-prev');
    const progressBar = document.getElementById('progress-bar');
    const volumeBar = document.getElementById('volume-bar');

    let currentPlaylist = []; // Lista utworów z obecnej strony
    let currentIndex = -1;

    // --- FUNKCJE STERUJĄCE ---

    function loadTrack(index) {
        if (index < 0 || index >= currentPlaylist.length) return;

        currentIndex = index;
        const track = currentPlaylist[index];

        audio.src = track.src;
        titleEl.textContent = track.title;
        artistEl.textContent = track.artist;

        playerBar.classList.remove('d-none'); // Pokaż pasek
        audio.play();
        updatePlayIcon(true);

        // Podświetl aktywny utwór na liście (opcjonalne)
        document.querySelectorAll('.play-btn').forEach(btn => {
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-success');
            btn.innerHTML = '<i class="bi bi-play-fill"></i>';
        });

        // Znajdź przycisk tego utworu i zmień go
        const activeBtn = document.querySelector(`.play-btn[data-src="${track.src}"]`);
        if (activeBtn) {
            activeBtn.classList.remove('btn-outline-success');
            activeBtn.classList.add('btn-success');
            activeBtn.innerHTML = '<i class="bi bi-pause-fill"></i>';
        }
    }

    function togglePlay() {
        if (audio.paused) {
            audio.play();
            updatePlayIcon(true);
        } else {
            audio.pause();
            updatePlayIcon(false);
        }
    }

    function updatePlayIcon(isPlaying) {
        playBtn.innerHTML = isPlaying ? '<i class="bi bi-pause-circle-fill fs-1"></i>' : '<i class="bi bi-play-circle-fill fs-1"></i>';
    }

    // --- AUTOMATYCZNE PRZEJŚCIE ---
    audio.addEventListener('ended', function() {
        if (currentIndex < currentPlaylist.length - 1) {
            loadTrack(currentIndex + 1); // Graj następny
        } else {
            updatePlayIcon(false); // Koniec playlisty
        }
    });

    // --- OBSŁUGA PRZYCISKÓW "PLAY" NA STRONIE ---
    // Ta funkcja musi być wywołana po każdym załadowaniu nowej strony (AJAX)
    window.initPlayButtons = function() {
        const buttons = document.querySelectorAll('.play-btn');
        currentPlaylist = []; // Reset playlisty

        buttons.forEach((btn, index) => {
            // Zbuduj playlistę z elementów na stronie
            currentPlaylist.push({
                src: btn.dataset.src,
                title: btn.dataset.title,
                artist: btn.dataset.artist
            });

            // Obsługa kliknięcia
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                // Jeśli klikamy w ten sam utwór -> pauza/play
                if (currentIndex === index) {
                    togglePlay();
                } else {
                    loadTrack(index);
                }
            });
        });
    };

    // --- EVENT LISTENERS ---
    playBtn.addEventListener('click', togglePlay);
    nextBtn.addEventListener('click', () => loadTrack(currentIndex + 1));
    prevBtn.addEventListener('click', () => loadTrack(currentIndex - 1));

    // Pasek postępu
    audio.addEventListener('timeupdate', () => {
        const progress = (audio.currentTime / audio.duration) * 100;
        progressBar.value = progress || 0;
    });

    progressBar.addEventListener('input', (e) => {
        const time = (e.target.value / 100) * audio.duration;
        audio.currentTime = time;
    });

    volumeBar.addEventListener('input', (e) => {
        audio.volume = e.target.value / 100;
    });

    // Inicjalizacja przycisków na start
    window.initPlayButtons();
});