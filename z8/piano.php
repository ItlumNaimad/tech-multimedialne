<?php include 'header.php'; ?>

<h2>Wirtualne Pianino (Zadanie 12 & 13)</h2>

<div class="card p-4 text-center">
    <div class="mb-3">
        <button class="btn btn-primary" onclick="playSequence()">▶ Odtwórz Sekwencję (Pętla)</button>
        <div id="staff-display" class="mt-3 fs-4 fw-bold text-info">Nuty: --</div>
    </div>

    <div class="piano-container d-flex justify-content-center position-relative" style="height: 200px; background: #333; padding: 10px;">
        <div class="key white" onclick="playNote('C')" data-note="C"></div>
        <div class="key white" onclick="playNote('D')" data-note="D"></div>
        <div class="key white" onclick="playNote('E')" data-note="E"></div>
        <div class="key white" onclick="playNote('F')" data-note="F"></div>
        <div class="key white" onclick="playNote('G')" data-note="G"></div>
        <div class="key white" onclick="playNote('A')" data-note="A"></div>
        <div class="key white" onclick="playNote('B')" data-note="B"></div>

        <div class="key black" onclick="playNote('C#')" style="left: 14%;" data-note="C#"></div>
        <div class="key black" onclick="playNote('D#')" style="left: 28%;" data-note="D#"></div>
        <div class="key black" onclick="playNote('F#')" style="left: 57%;" data-note="F#"></div>
        <div class="key black" onclick="playNote('G#')" style="left: 71%;" data-note="G#"></div>
        <div class="key black" onclick="playNote('A#')" style="left: 85%;" data-note="A#"></div>
    </div>
</div>

<style>
    .key { cursor: pointer; border-radius: 0 0 5px 5px; transition: 0.1s; }
    .white { width: 14%; height: 100%; background: white; border: 1px solid #ccc; z-index: 1; }
    .white:active, .white.active { background: #eee; transform: scale(0.98); }
    .black { width: 8%; height: 60%; background: black; position: absolute; top: 10px; z-index: 2; }
    .black:active, .black.active { background: #333; }
</style>

<script>
    // Prosty syntezator dźwięku (Web Audio API)
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();

    function playNote(note) {
        // Mapowanie nut na częstotliwości
        const freqs = {
            'C': 261.63, 'C#': 277.18, 'D': 293.66, 'D#': 311.13, 'E': 329.63,
            'F': 349.23, 'F#': 369.99, 'G': 392.00, 'G#': 415.30, 'A': 440.00,
            'A#': 466.16, 'B': 493.88
        };

        if (!freqs[note]) return;

        // Wizualizacja "kliknięcia"
        const keys = document.querySelectorAll('.key');
        keys.forEach(k => {
            if(k.dataset.note === note) {
                k.classList.add('active');
                setTimeout(() => k.classList.remove('active'), 200);
            }
        });

        // Generowanie dźwięku
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine'; // Typ fali (pianino-podobne)
        osc.frequency.value = freqs[note];

        osc.connect(gain);
        gain.connect(audioCtx.destination);

        osc.start();
        // Wybrzmiewanie (fade out)
        gain.gain.exponentialRampToValueAtTime(0.00001, audioCtx.currentTime + 1);
        osc.stop(audioCtx.currentTime + 1);

        // Wyświetlanie na "pięciolinii" (tekstowo dla uproszczenia)
        document.getElementById('staff-display').innerText = "Grana nuta: " + note;
    }

    // Zadanie 13: Odtwarzanie sekwencji
    function playSequence() {
        const melody = ['E', 'D#', 'E', 'D#', 'E', 'B', 'D', 'C', 'A']; // Dla Elizy (fragment)
        let delay = 0;

        melody.forEach((note, index) => {
            setTimeout(() => {
                playNote(note);
            }, delay);
            delay += 400; // Szybkość grania
        });
    }
</script>

</body>
</html>