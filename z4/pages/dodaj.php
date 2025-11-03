<?php
// Tylko dla zalogowanych
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php?page=logowanie');
    exit();
}
?>

<h3>Dodaj nowy host do monitorowania (Pkt 13)</h3>
<p>Wprowadź adres hosta (np. `pbs.edu.pl`) oraz port usługi (np. `443` dla HTTPS).</p>

<div class="row">
    <div class="col-md-6">
        <form action="dodaj_handler.php" method="post">
            <div class="mb-3">
                <label for="host" class="form-label">Nazwa Hosta lub IP</label>
                <input type="text" class="form-control" id="host" name="host" required>
            </div>
            <div class="mb-3">
                <label for="port" class="form-label">Port</label>
                <input type="number" class="form-control" id="port" name="port" value="80" required>
            </div>
            <button type="submit" class="btn btn-primary">Dodaj do monitora</button>
            <a href="index.php?page=home" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
</div>