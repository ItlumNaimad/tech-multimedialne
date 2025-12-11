<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card bg-dark text-white border-secondary">
            <div class="card-header border-secondary">Stwórz nową playlistę</div>
            <div class="card-body">
                <form action="scripts/create_playlist_handler.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nazwa playlisty</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="public" id="publicCheck">
                        <label class="form-check-label" for="publicCheck">
                            Playlista Publiczna (widoczna dla wszystkich)
                        </label>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Utwórz</button>
                </form>
            </div>
        </div>
    </div>
</div>