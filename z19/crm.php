<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = getCurrentUserId();
$is_admin = isModerator(); // Funkcja z auth.php sprawdzająca czy user = admin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_ticket') {
        $msg = trim($_POST['message'] ?? '');
        if (!empty($msg)) {
            $stmt = $pdo->prepare("INSERT INTO crm_tickets (user_id, message) VALUES (?, ?)");
            $stmt->execute([$user_id, $msg]);
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'resolve_ticket' && $is_admin) {
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        $reply = trim($_POST['reply'] ?? 'Zgłoszenie rozwiązane przez obsługę.');
        if ($ticket_id > 0) {
            $stmt = $pdo->prepare("UPDATE crm_tickets SET status = 'resolved', reply = ? WHERE id = ?");
            $stmt->execute([$reply, $ticket_id]);
        }
    }
    header("Location: crm.php");
    exit;
}

if ($is_admin) {
    // Admin widzi wszystko
    $stmt = $pdo->query("SELECT t.*, u.username FROM crm_tickets t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC");
    $tickets = $stmt->fetchAll();
} else {
    // User widzi tylko swoje
    $stmt = $pdo->prepare("SELECT t.*, u.username FROM crm_tickets t JOIN users u ON t.user_id = u.id WHERE t.user_id = ? ORDER BY t.created_at DESC");
    $stmt->execute([$user_id]);
    $tickets = $stmt->fetchAll();
}

require_once 'header.php';
?>

<div class="row">
    <div class="col-md-12">
        <h3 class="text-danger mb-4"><i class="bi bi-headset"></i> CRM: Obsługa Reklamacji i Zgłoszeń</h3>
    </div>
</div>

<div class="row">
    <?php if(!$is_admin): ?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Zgłoś nową sprawę</h5>
                <form method="post">
                    <input type="hidden" name="action" value="add_ticket">
                    <div class="mb-3">
                        <label class="form-label">Opis problemu (Reklamacja)</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100"><i class="bi bi-send"></i> Wyślij zgłoszenie</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="col-md-<?= $is_admin ? '12' : '8' ?>">
        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista zgłoszeń <?= $is_admin ? '(Widok Managera/Admina)' : '(Twoje)' ?></h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Klient</th>
                            <th>Treść zgłoszenia</th>
                            <th>Status</th>
                            <?php if($is_admin): ?><th>Akcja</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($tickets)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">Brak zgłoszeń w systemie.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach($tickets as $t): ?>
                        <tr>
                            <td>#<?= $t['id'] ?></td>
                            <td><strong><?= htmlspecialchars($t['username']) ?></strong></td>
                            <td>
                                <?= nl2br(htmlspecialchars($t['message'])) ?>
                                <?php if($t['reply']): ?>
                                    <div class="mt-2 p-2 bg-light border-start border-3 border-success small">
                                        <strong>Odpowiedź obsługi:</strong> <?= htmlspecialchars($t['reply']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $t['status'] === 'resolved' ? '<span class="badge bg-success">Zrealizowane</span>' : '<span class="badge bg-warning text-dark">Oczekujące</span>' ?>
                            </td>
                            <?php if($is_admin): ?>
                            <td>
                                <?php if($t['status'] === 'open'): ?>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="action" value="resolve_ticket">
                                    <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                                    <input type="text" name="reply" class="form-control form-control-sm" placeholder="Odpowiedź..." required>
                                    <button class="btn btn-sm btn-success text-nowrap"><i class="bi bi-check2"></i> Rozwiąż</button>
                                </form>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
