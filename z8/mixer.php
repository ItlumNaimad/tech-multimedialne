<?php include 'header.php'; ?>

<h2>Mikser Audio (Zadanie 10 & 11)</h2>

<div class="card p-4 mb-4">
    <h4>Classical Mixer (Predefiniowany)</h4>
    <div class="row" id="preset-mixer">
        <?php
        $tracks = [
            ['name' => 'Klasyk Oryginał', 'file' => 'media/classic1.mp3'],
            ['name' => 'Klasyk Cover', 'file' => 'media/classic1_cover.mp3'],
            ['name' => 'Audacity Mix', 'file' => 'media/voice_mix.mp3'],
            ['name' => 'Dodatkowy', 'file' => 'media/rock1.mp3']
        ];

        foreach($tracks as $index => $track):
            ?>
            <div class="col-md-3 text-center">
                <label><?= $track['name'] ?></label>
                <input type="range" class="form-range" min="0" max="1" step="0.01" value="1"
                       oninput="setVolume('audio<?= $index ?>', this.value)">
                <audio id="audio<?= $index ?>" src="<?= $track['file'] ?>" loop controls class="w-100 mt-2"></audio>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="mt-3 text-center">
        <button class="btn btn-success" onclick="playAll()">Odpal Wszystkie</button>
        <button class="btn btn-danger" onclick="stopAll()">Zatrzymaj Wszystkie</button>
    </div>
</div>

<div class="card p-4">
    <h4>Test Mixer (Wgraj własne pliki)</h4>
    <div class="row">
        <?php for($i=1; $i<=4; $i++): ?>
            <div class="col-md-3 text-center border p-2">
                <h6>Kanał <?= $i ?></h6>
                <input type="file" class="form-control mb-2" accept="audio/*" onchange="loadFile(event, 'testAudio<?= $i ?>')">
                <input type="range" class="form-range" min="0" max="1" step="0.01" value="1"
                       oninput="setVolume('testAudio<?= $i ?>', this.value)">
                <audio id="testAudio<?= $i ?>" controls class="w-100"></audio>
            </div>
        <?php endfor; ?>
    </div>
</div>

<script>
    function setVolume(id, val) {
        document.getElementById(id).volume = val;
    }

    function playAll() {
        document.querySelectorAll('#preset-mixer audio').forEach(a => a.play());
    }

    function stopAll() {
        document.querySelectorAll('#preset-mixer audio').forEach(a => {
            a.pause();
            a.currentTime = 0;
        });
    }

    // Obsługa ładowania plików lokalnych (Zad 11)
    function loadFile(event, audioId) {
        const file = event.target.files[0];
        if (file) {
            const audio = document.getElementById(audioId);
            audio.src = URL.createObjectURL(file);
        }
    }
</script>

</body>
</html>