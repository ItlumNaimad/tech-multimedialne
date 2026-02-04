<?php include 'header.php'; ?>

<h2 class="mb-4">Muzyka Klasyczna (Covery MIDI)</h2>

<div class="card p-4">
    <div class="row">
        <div class="col-md-6 border-end border-secondary">
            <h4 class="text-success">Oryginał</h4>
            <p><strong>Autor:</strong> Ktoś inny <br> <strong>Utwór:</strong> jakiś inny</p>

            <p>MP3:</p>
            <audio controls class="w-100 mb-3">
                <source src="media/classic1.mp3" type="audio/mpeg">
            </audio>

            <p>MIDI:</p>
            <midi-player src="media/classic1.mid" sound-font visualizer="#vis1"></midi-player>
        </div>
        <div class="col-md-6">
            <h4 class="text-warning">Mój Cover</h4>
            <p>Cover na </p>

            <p>MP3:</p>
            <audio controls class="w-100 mb-3">
                <source src="media/classic1_cover.mp3" type="audio/mpeg">
            </audio>

            <p>MIDI:</p>
            <midi-player src="media/classic1_cover.mid" sound-font visualizer="#vis1"></midi-player>
        </div>
    </div>
    <midi-visualizer type="piano-roll" id="vis1"></midi-visualizer>
</div>

</body>
</html>