<?php
session_start();
require_once 'db_connect.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['typ'] != 'pracownik') {
    header('Location: login.php?typ=pracownik'); exit;
}

$user_id = $_SESSION['user']['id'];
$spec_id = $_SESSION['spec_id'] ?? null;

// Wybór specjalizacji
if (isset($_POST['set_spec'])) {
    $_SESSION['spec_id'] = $_POST['spec_id'];
    $spec_id = $_POST['spec_id'];
    header('Location: worker.php'); exit;
}

// Odpowiedź na zgłoszenie
if (isset($_POST['reply_ticket'])) {
    $zgl_id = $_POST['zgl_id'];
    $tresc = $_POST['tresc'];
    
    // Przypisanie pracownika jeśli to pierwsza odpowiedź
    $stmt = $pdo->prepare("UPDATE zgloszenia SET id_pracownika = ? WHERE id = ? AND id_pracownika IS NULL");
    $stmt->execute([$user_id, $zgl_id]);
    
    $stmt = $pdo->prepare("INSERT INTO wiadomosci (id_zgloszenia, id_autora, tresc) VALUES (?, ?, ?)");
    $stmt->execute([$zgl_id, $user_id, $tresc]);
    header("Location: worker.php?view=$zgl_id"); exit;
}

$zagadnienia = $pdo->query("SELECT * FROM zagadnienia")->fetchAll();

$open_tickets = [];
if ($spec_id) {
    $stmt = $pdo->prepare("SELECT z.*, u.nazwisko as klient_nazwisko FROM zgloszenia z JOIN uzytkownicy u ON z.id_klienta = u.id WHERE z.id_zagadnienia = ? AND z.status = 'otwarte' AND (z.id_pracownika = ? OR z.id_pracownika IS NULL) ORDER BY z.data_utworzenia ASC");
    $stmt->execute([$spec_id, $user_id]);
    $open_tickets = $stmt->fetchAll();
}

$view_id = $_GET['view'] ?? null;
$view_ticket = null;
$messages = [];
if ($view_id) {
    $stmt = $pdo->prepare("SELECT z.*, u.nazwisko as klient_nazwisko FROM zgloszenia z JOIN uzytkownicy u ON z.id_klienta = u.id WHERE z.id = ?");
    $stmt->execute([$view_id]);
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
    <title>Panel Pracownika - WHS CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .msg-klient { background: #f0f2f5; margin-right: 20%; }
        .msg-pracownik { background: #e7f3ff; margin-left: 20%; text-align: right; }
        .msg-box { border-radius: 15px; padding: 10px 15px; margin-bottom: 10px; }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow">
    <div class="container">
        <a class="navbar-brand" href="index.php">WHS CRM - Panel Pracownika</a>
        <span class="navbar-text text-white">Pracownik: <?= htmlspecialchars($_SESSION['user']['nazwisko']) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Wyloguj</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Twoja Specjalizacja</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <select name="spec_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Wybierz kategorię --</option>
                                <?php foreach($zagadnienia as $z): ?>
                                    <option value="<?= $z['id'] ?>" <?= $spec_id == $z['id'] ? 'selected' : '' ?>><?= $z['nazwa'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="set_spec" value="1">
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-secondary text-white">Zgłoszenia Oczekujące</div>
                <div class="list-group list-group-flush">
                    <?php foreach($open_tickets as $t): ?>
                        <a href="?view=<?= $t['id'] ?>" class="list-group-item list-group-item-action <?= $view_id == $t['id'] ? 'active' : '' ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1 text-truncate" style="max-width: 150px;"><?= htmlspecialchars($t['temat']) ?></h6>
                                <small><?= $t['id_pracownika'] ? '👤' : '🆕' ?></small>
                            </div>
                            <small class="<?= $view_id == $t['id'] ? 'text-white' : 'text-muted' ?>">Klient: <?= htmlspecialchars($t['klient_nazwisko']) ?></small>
                        </a>
                    <?php endforeach; ?>
                    <?php if(empty($open_tickets)): ?>
                        <div class="p-3 text-center text-muted">Brak otwartych zgłoszeń w tej kategorii.</div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <div class="card-header bg-secondary text-white">W trakcie rozwiązywania</div>
                        <?php 
                        $in_progress = [];
                        if ($spec_id) {
                            $stmt = $pdo->prepare("SELECT z.*, u.nazwisko as klient_nazwisko FROM zgloszenia z JOIN uzytkownicy u ON z.id_klienta = u.id WHERE z.id_zagadnienia = ? AND z.status = 'w_trakcie' AND z.id_pracownika = ? ORDER BY z.data_utworzenia DESC");
                            $stmt->execute([$spec_id, $user_id]);
                            $in_progress = $stmt->fetchAll();
                        }
                        foreach($in_progress as $t): ?>
                            <a href="?view=<?= $t['id'] ?>" class="list-group-item list-group-item-warning list-group-item-action <?= $view_id == $t['id'] ? 'active bg-warning text-dark' : '' ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 text-truncate" style="max-width: 150px;"><?= htmlspecialchars($t['temat']) ?></h6>
                                </div>
                                <small>Klient: <?= htmlspecialchars($t['klient_nazwisko']) ?></small>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <?php if($view_ticket): ?>
                <div class="card shadow">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><?= htmlspecialchars($view_ticket['temat']) ?> <small class="text-muted">(Od: <?= htmlspecialchars($view_ticket['klient_nazwisko']) ?>)</small></h5>
                        <?php
                            $status_color = $view_ticket['status'] == 'otwarte' ? 'bg-success' : ($view_ticket['status'] == 'w_trakcie' ? 'bg-warning text-dark' : 'bg-danger');
                            $status_texts = ['otwarte' => 'OTWARTE', 'w_trakcie' => 'W TRAKCIE ROZWIĄZYWANIA', 'zamkniete' => 'ZAMKNIĘTE'];
                        ?>
                        <span class="badge <?= $status_color ?>" id="ticket-status" <?= $view_ticket['status'] == 'otwarte' ? 'style="cursor: pointer;" onclick="changeStatus('.$view_ticket['id'].', \'w_trakcie\')"' : '' ?> title="<?= $view_ticket['status'] == 'otwarte' ? 'Kliknij, aby przejąć zgłoszenie' : '' ?>"><?= $status_texts[$view_ticket['status']] ?? strtoupper($view_ticket['status']) ?></span>
                    </div>
                    <div class="card-body" id="chat-box" style="height: 400px; overflow-y: auto;">
                        <?php foreach($messages as $m): ?>
                            <div class="msg-box <?= $m['typ'] == 'klient' ? 'msg-klient' : 'msg-pracownik' ?>">
                                <strong><?= $m['typ'] == 'klient' ? 'Klient: ' . htmlspecialchars($m['nazwisko']) : 'Ty' ?></strong>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($m['tresc'])) ?></p>
                                <small class="text-muted" style="font-size: 0.75rem;"><?= $m['data_godzina'] ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if($view_ticket['status'] != 'zamkniete'): ?>
                        <div class="card-footer">
                            <form method="POST" class="d-flex" id="chat-form">
                                <input type="hidden" name="zgl_id" value="<?= $view_ticket['id'] ?>">
                                <input type="text" name="tresc" class="form-control me-2" placeholder="Twoja odpowiedź..." required>
                                <button type="submit" name="reply_ticket" class="btn btn-success">Odpowiedz</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card-footer bg-light text-center">
                            To zgłoszenie zostało zamknięte.
                            <?php if($view_ticket['ocena']): ?>
                                <br>Twoja ocena: <strong><?= str_repeat('⭐', $view_ticket['ocena']) ?> (<?= $view_ticket['ocena'] ?>/5)</strong>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-5">Wybierz zgłoszenie z listy oczekujących.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($view_ticket): ?>
<script>
let lastMsgId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
const zglId = <?= $view_ticket['id'] ?>;

function fetchMessages() {
    fetch(`api_chat.php?zgl_id=${zglId}&last_id=${lastMsgId}`)
        .then(res => res.json())
        .then(data => {
            if (data.messages && data.messages.length > 0) {
                const chatBox = document.getElementById('chat-box');
                data.messages.forEach(msg => {
                    const isClient = msg.typ === 'klient';
                    const div = document.createElement('div');
                    div.className = 'msg-box ' + (isClient ? 'msg-klient' : 'msg-pracownik');
                    
                    const p = document.createElement('p');
                    p.className = 'mb-0';
                    p.innerText = msg.tresc; 
                    p.innerHTML = p.innerHTML.replace(/\n/g, '<br>');

                    div.innerHTML = `<strong>${isClient ? 'Klient: ' + msg.nazwisko : 'Ty'}</strong>
                                     ${p.outerHTML}
                                     <small class="text-muted" style="font-size: 0.75rem;">${msg.data_godzina}</small>`;
                    chatBox.appendChild(div);
                    lastMsgId = msg.id;
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            }
            if (data.status === 'zamkniete' && '<?= $view_ticket['status'] ?>' !== 'zamkniete') {
                location.reload(); 
            }
            if (data.status === 'w_trakcie') {
                const badge = document.getElementById('ticket-status');
                if (badge && badge.innerText === 'OTWARTE') {
                    badge.className = 'badge bg-warning text-dark';
                    badge.innerText = 'W TRAKCIE ROZWIĄZYWANIA';
                    badge.style.cursor = 'default';
                    badge.title = '';
                    badge.onclick = null;
                }
            }
        });
}
setInterval(fetchMessages, 3000);

const chatForm = document.getElementById('chat-form');
if (chatForm) {
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const trescInput = this.querySelector('[name="tresc"]');
        const tresc = trescInput.value;
        if (!tresc) return;
        
        trescInput.disabled = true;
        const formData = new FormData();
        formData.append('zgl_id', zglId);
        formData.append('tresc', tresc);
        
        fetch('api_chat.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    trescInput.value = '';
                    fetchMessages();
                }
                trescInput.disabled = false;
                trescInput.focus();
            });
    });
}

function changeStatus(id, newStatus) {
    fetch('api_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ zgl_id: id, status: newStatus })
    }).then(res => res.json()).then(data => {
        if (data.success) {
            const badge = document.getElementById('ticket-status');
            badge.className = 'badge bg-warning text-dark';
            badge.innerText = 'W TRAKCIE ROZWIĄZYWANIA';
            badge.style.cursor = 'default';
            badge.title = '';
            badge.onclick = null;
        }
    });
}

const chatBox = document.getElementById('chat-box');
if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>
<?php endif; ?>

</body>
</html>
