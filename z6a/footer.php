<div id="global-player-bar" class="fixed-bottom bg-dark border-top border-secondary text-white p-2 d-none" style="z-index: 1050;">
    <div class="container d-flex align-items-center justify-content-between">

        <div class="d-flex align-items-center" style="width: 30%;">
            <div class="bg-secondary d-flex align-items-center justify-content-center rounded me-3" style="width: 50px; height: 50px;">
                <i class="bi bi-music-note-beamed fs-3"></i>
            </div>
            <div class="text-truncate">
                <h6 class="mb-0 fw-bold" id="player-title">Wybierz utwór</h6>
                <small class="text-muted" id="player-artist">...</small>
            </div>
        </div>

        <div class="d-flex flex-column align-items-center flex-grow-1 mx-3">
            <div class="d-flex gap-3 mb-1">
                <button class="btn btn-sm btn-link text-secondary" id="btn-prev"><i class="bi bi-skip-start-fill fs-4"></i></button>
                <button class="btn btn-sm btn-link text-white p-0" id="btn-play-pause"><i class="bi bi-play-circle-fill fs-1"></i></button>
                <button class="btn btn-sm btn-link text-secondary" id="btn-next"><i class="bi bi-skip-end-fill fs-4"></i></button>
            </div>
            <input type="range" class="form-range" id="progress-bar" value="0" min="0" max="100" style="height: 4px;">
        </div>

        <div class="d-flex align-items-center" style="width: 20%; justify-content: flex-end;">
            <i class="bi bi-volume-up me-2"></i>
            <input type="range" class="form-range" id="volume-bar" value="100" min="0" max="100" style="width: 100px;">
        </div>

        <audio id="main-audio"></audio>
    </div>
</div>

<script src="js/player.js"></script>