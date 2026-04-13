<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['typ'] != 'klient') {
    header('Location: login.php?typ=klient'); exit;
}

$user_id = $_SESSION['user']['id'];
$success_msg = '';

// Obsługa nowego zgłoszenia
if (isset($_POST['new_ticket'])) {
    $idz = $_POST['idz'];
    $temat = $_POST['temat'];
    $tresc = $_POST['tresc'];
    
    $stmt = $pdo->prepare("INSERT INTO zgloszenia (id_klienta, id_zagadnienia, temat) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $idz, $temat]);
    $zgl_id = $pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO wiadomosci (id_zgloszenia, id_autora, tresc) VALUES (?, ?, ?)");
    $stmt->execute([$zgl_id, $user_id, $tresc]);
    $success_msg = "Zgłoszenie zostało wysłane!";
}

// Obsługa odpowiedzi w istniejącym zgłoszeniu (komunikator)
if (isset($_POST['reply_ticket'])) {
    $zgl_id = $_POST['zgl_id'];
    $tresc = $_POST['tresc'];
    $stmt = $pdo->prepare("INSERT INTO wiadomosci (id_zgloszenia, id_autora, tresc) VALUES (?, ?, ?)");
    $stmt->execute([$zgl_id, $user_id, $tresc]);
    header("Location: client.php?view=$zgl_id"); exit;
}

// Obsługa oceny
if (isset($_POST['rate_ticket'])) {
    $zgl_id = $_POST['zgl_id'];
    $ocena = $_POST['ocena'];
    $stmt = $pdo->prepare("UPDATE zgloszenia SET ocena = ?, status = 'zamkniete' WHERE id = ? AND id_klienta = ?");
    $stmt->execute([$ocena, $zgl_id, $user_id]);
    header("Location: client.php?view=$zgl_id"); exit;
}

$zagadnienia = $pdo->query("SELECT * FROM zagadnienia")->fetchAll();
$my_tickets = $pdo->prepare("SELECT z.*, zag.nazwa as zag_nazwa FROM zgloszenia z JOIN zagadnienia zag ON z.id_zagadnienia = zag.id WHERE z.id_klienta = ? ORDER BY z.data_utworzenia DESC");
$my_tickets->execute([$user_id]);
$tickets = $my_tickets->fetchAll();

$view_id = $_GET['view'] ?? null;
$view_ticket = null;
$messages = [];
if ($view_id) {
    $stmt = $pdo->prepare("SELECT z.*, u.nazwisko as pracownik_nazwisko FROM zgloszenia z LEFT JOIN uzytkownicy u ON z.id_pracownika = u.id WHERE z.id = ? AND z.id_klienta = ?");
    $stmt->execute([$view_id, $user_id]);
    $view_ticket = $stmt->fetch();
    
    if ($view_ticket) {
        $stmt = $pdo->prepare("SELECT w.*, u.nazwisko, u.typ FROM wiadomosci w JOIN uzytkownicy u ON w.id_autora = u.id WHERE w.id_zgloszenia = ? ORDER BY w.data_godzina ASC");
        $stmt->execute([$view_id]);
        $messages = $stmt->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Klienta - WHS CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .msg-klient { background: #e7f3ff; margin-left: 20%; text-align: right; }
        .msg-pracownik { background: #f0f2f5; margin-right: 20%; }
        .msg-box { border-radius: 15px; padding: 10px 15px; margin-bottom: 10px; }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand" href="index.php">WHS CRM - Panel Klienta</a>
        <span class="navbar-text text-white">Witaj, <?= htmlspecialchars($_SESSION['user']['nazwisko']) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Wyloguj</a>
    </div>
</nav>

<div class="container mt-4">
    <?php if($success_msg): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Nowe Zgłoszenie</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Zagadnienie</label>
                            <select name="idz" class="form-select" required>
                                <?php foreach($zagadnienia as $z): ?>
                                    <option value="<?= $z['id'] ?>"><?= $z['nazwa'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Temat</label>
                            <input type="text" name="temat" class="form-control" required placeholder="Krótki opis problemu">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Treść pytania</label>
                            <textarea name="tresc" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="new_ticket" class="btn btn-primary w-100">Wyślij Zgłoszenie</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary text-white">Twoje Zgłoszenia</div>
                <div class="list-group list-group-flush">
                    <?php foreach($tickets as $t): ?>
                        <a href="?view=<?= $t['id'] ?>" class="list-group-item list-group-item-action <?= $view_id == $t['id'] ? 'active' : '' ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-truncate" style="max-width: 150px;"><?= htmlspecialchars($t['temat']) ?></h6>
                                <small><?= $t['status'] == 'otwarte' ? '🟢' : '🔴' ?></small>
                            </div>
                            <small class="<?= $view_id == $t['id'] ? 'text-white' : 'text-muted' ?>"><?= $t['zag_nazwa'] ?></small>
                        </a>
                    <?php endforeach; ?>
                    <?php if(empty($tickets)): ?>
                        <div class="p-3 text-center text-muted">Brak zgłoszeń.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?php if($view_ticket): ?>
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><?= htmlspecialchars($view_ticket['temat']) ?></h5>
                        <span class="badge <?= $view_ticket['status'] == 'otwarte' ? 'bg-success' : 'bg-danger' ?>"><?= strtoupper($view_ticket['status']) ?></span>
                    </div>
                    <div class="card-body" style="height: 400px; overflow-y: auto;">
                        <?php foreach($messages as $m): ?>
                            <div class="msg-box <?= $m['typ'] == 'klient' ? 'msg-klient' : 'msg-pracownik' ?>">
                                <strong><?= $m['typ'] == 'pracownik' ? 'Pracownik: ' . htmlspecialchars($m['nazwisko']) : 'Ty' ?></strong>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($m['tresc'])) ?></p>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= $m['data_godzina'] ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if($view_ticket['status'] == 'otwarte'): ?>
                        <div class="card-footer">
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="zgl_id" value="<?= $view_ticket['id'] ?>">
                                <input type="text" name="tresc" class="form-control me-2" placeholder="Napisz wiadomość..." required>
                                <button type="submit" name="reply_ticket" class="btn btn-primary">Wyślij</button>
                            </form>
                            <?php if($view_ticket['pracownik_nazwisko']): ?>
                                <hr>
                                <form method="POST" class="row g-2 align-items-center">
                                    <input type="hidden" name="zgl_id" value="<?= $view_ticket['id'] ?>">
                                    <div class="col-auto"><label>Oceń obsługę:</label></div>
                                    <div class="col-auto">
                                        <select name="ocena" class="form-select form-select-sm">
                                            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                            <option value="4">⭐⭐⭐⭐ (4)</option>
                                            <option value="3">⭐⭐⭐ (3)</option>
                                            <option value="2">⭐⭐ (2)</option>
                                            <option value="1">⭐ (1)</option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" name="rate_ticket" class="btn btn-sm btn-warning">Oceń i Zamknij</button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php elseif($view_ticket['ocena']): ?>
                        <div class="card-footer bg-light text-center">
                            Twoja ocena: <strong><?= str_repeat('⭐', $view_ticket['ocena']) ?> (<?= $view_ticket['ocena'] ?>/5)</strong>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-5">Wybierz zgłoszenie z listy po lewej lub stwórz nowe.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
