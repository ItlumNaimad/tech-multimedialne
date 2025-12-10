document.addEventListener('DOMContentLoaded', function() {
    const audio = document.getElementById('global-audio-player');
    const playerBar = document.getElementById('global-player-bar');
    const titleEl = document.getElementById('player-title');
    const artistEl = document.getElementById('player-artist');
    const playPauseBtn = document.getElementById('btn-play-pause');
    const playPauseIcon = playPauseBtn.querySelector('i');
    const nextBtn = document.getElementById('btn-next');
    const prevBtn = document.getElementById('btn-prev');
    const loopBtn = document.getElementById('btn-loop');
    const progressBar = document.getElementById('progress-bar');
    const volumeBar = document.getElementById('volume-bar');
    const timeCurrentEl = document.getElementById('time-current');
    const timeTotalEl = document.getElementById('time-total');

    let currentPlaylist = [];
    let currentIndex = -1;
    let isLooping = false;
    let currentButton = null;

    function collectSongs() {
        currentPlaylist = [];
        const songButtons = document.querySelectorAll('.play-btn');
        songButtons.forEach(btn => {
            currentPlaylist.push({
                src: btn.dataset.src,
                title: btn.dataset.title,
                artist: btn.dataset.artist,
                element: btn 
            });
        });
    }

    function playSong(index) {
        if (index < 0 || index >= currentPlaylist.length) {
            if (isLooping && currentPlaylist.length > 0) {
                index = 0; // Wróć na początek, jeśli pętla jest włączona
            } else {
                updatePlayIcon(false);
                return; // Zatrzymaj, jeśli nie ma pętli
            }
        }

        currentIndex = index;
        const track = currentPlaylist[index];
        currentButton = track.element;

        audio.src = track.src;
        titleEl.textContent = track.title;
        artistEl.textContent = track.artist;

        playerBar.classList.remove('d-none');
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

    function updatePlayIcon(isPlaying) {
        if (isPlaying) {
            playPauseIcon.classList.remove('bi-play-circle-fill');
            playPauseIcon.classList.add('bi-pause-circle-fill');
        } else {
            playPauseIcon.classList.remove('bi-pause-circle-fill');
            playPauseIcon.classList.add('bi-play-circle-fill');
        }
    }
    
    function formatTime(seconds) {
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return m + ':' + (s < 10 ? '0' : '') + s;
    }

    // --- Event Listeners ---

    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.play-btn');
        if (btn) {
            e.preventDefault();
            collectSongs(); // Zbieramy utwory przy każdym kliknięciu
            const songIndex = currentPlaylist.findIndex(t => t.element === btn);

            if (currentIndex === songIndex && !audio.paused) {
                audio.pause();
            } else if (currentIndex === songIndex && audio.paused) {
                audio.play();
            } else {
                playSong(songIndex);
            }
        }
    });


    playPauseBtn.addEventListener('click', () => {
        if (audio.src) {
            audio.paused ? audio.play() : audio.pause();
        }
    });

    audio.addEventListener('play', () => updatePlayIcon(true));
    audio.addEventListener('pause', () => updatePlayIcon(false));

    audio.addEventListener('ended', () => {
        playSong(currentIndex + 1);
    });
    
    audio.addEventListener('loadedmetadata', () => {
        timeTotalEl.textContent = formatTime(audio.duration);
        progressBar.max = audio.duration;
    });

    audio.addEventListener('timeupdate', () => {
        timeCurrentEl.textContent = formatTime(audio.currentTime);
        progressBar.value = audio.currentTime;
    });

    progressBar.addEventListener('input', () => {
        audio.currentTime = progressBar.value;
    });

    nextBtn.addEventListener('click', () => playSong(currentIndex + 1));
    prevBtn.addEventListener('click', () => playSong(currentIndex - 1));

    loopBtn.addEventListener('click', () => {
        isLooping = !isLooping;
        loopBtn.classList.toggle('text-success', isLooping); // Użyj klasy Bootstrapa
        loopBtn.classList.toggle('text-secondary', !isLooping);
    });
    
    volumeBar.addEventListener('input', (e) => {
        audio.volume = e.target.value;
    });

    // Inicjalizacja na start
    collectSongs();
});