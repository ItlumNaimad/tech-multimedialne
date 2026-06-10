<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$topic_id = (int)($_GET['id'] ?? 0);

if ($topic_id <= 0) {
    header("Location: forum.php");
    exit;
}

// System cenzurowania (re-użycie logiki z forum.php)
function censorContent($text) {
    $bad_words = ['kurwa', 'chuj', 'jebany', 'spierdalaj', 'debil', 'idiota'];
    foreach ($bad_words as $word) {
        $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
        $text = preg_replace($pattern, '***', $text);
    }
    return $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_reply') {
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($content)) {
        $censoredContent = censorContent($content);
        $stmt = $pdo->prepare("INSERT INTO forum_replies (topic_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$topic_id, getCurrentUserId(), $censoredContent]);
    }
    header("Location: forum_topic.php?id=" . $topic_id);
    exit;
}

// Pobieranie tematu
$stmt = $pdo->prepare("SELECT f.*, u.username FROM forum_posts f JOIN users u ON f.user_id = u.id WHERE f.id = ?");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch();

if (!$topic) {
    header("Location: forum.php");
    exit;
}

// Pobieranie odpowiedzi
$stmt = $pdo->prepare("SELECT r.*, u.username FROM forum_replies r JOIN users u ON r.user_id = u.id WHERE r.topic_id = ? ORDER BY r.created_at ASC");
$stmt->execute([$topic_id]);
$replies = $stmt->fetchAll();

require_once 'header.php';
?>

<div class="row mb-4">
    <div class="col-md-12">
        <a href="forum.php" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Wróć do listy tematów</a>
        
        <!-- Główny post -->
        <div class="card shadow-sm border-info">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?= htmlspecialchars($topic['topic']) ?></h4>
                <small><i class="bi bi-clock"></i> <?= date('d.m.Y H:i', strtotime($topic['created_at'])) ?></small>
            </div>
            <div class="card-body p-4">
                <p class="fs-5"><?= nl2br(htmlspecialchars($topic['content'])) ?></p>
            </div>
            <div class="card-footer text-muted text-end">
                Napisał: <strong><?= htmlspecialchars($topic['username']) ?></strong>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h5 class="mb-3">Odpowiedzi w wątku (<?= count($replies) ?>)</h5>
        
        <?php if(empty($replies)): ?>
            <div class="alert alert-light">Nikt jeszcze nie odpowiedział w tym wątku.</div>
        <?php else: ?>
            <div class="list-group shadow-sm mb-4">
                <?php foreach($replies as $r): ?>
                <div class="list-group-item p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <strong><i class="bi bi-person-fill"></i> <?= htmlspecialchars($r['username']) ?></strong>
                        <small class="text-muted"><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></small>
                    </div>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($r['content'])) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Formularz odpowiedzi -->
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <form method="post">
                    <h6 class="mb-2">Napisz odpowiedź</h6>
                    <input type="hidden" name="action" value="add_reply">
                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="3" placeholder="Twoja odpowiedź (z cenzurą)..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-reply"></i> Odpowiedz</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
