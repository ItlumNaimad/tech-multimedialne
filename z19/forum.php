<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// System cenzurowania (Phrase Moderation)
function censorContent($text) {
    $bad_words = ['kurwa', 'chuj', 'jebany', 'spierdalaj', 'debil', 'idiota'];
    foreach ($bad_words as $word) {
        // prosta cenzura wielkości liter
        $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
        $text = preg_replace($pattern, '***', $text);
    }
    return $text;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_post') {
    $topic = trim($_POST['topic'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($topic) && !empty($content)) {
        // Zastosowanie cenzury przed zapisem do bazy
        $censoredTopic = censorContent($topic);
        $censoredContent = censorContent($content);
        
        $stmt = $pdo->prepare("INSERT INTO forum_posts (user_id, topic, content) VALUES (?, ?, ?)");
        $stmt->execute([getCurrentUserId(), $censoredTopic, $censoredContent]);
    }
    header("Location: forum.php");
    exit;
}

// Pobieranie postów
$stmt = $pdo->query("SELECT f.*, u.username FROM forum_posts f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
$posts = $stmt->fetchAll();

require_once 'header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm mb-4 border-info">
            <div class="card-body">
                <h3 class="card-title text-info"><i class="bi bi-chat-text"></i> Forum Dyskusyjne (z cenzurą)</h3>
                <p class="text-muted">Zastępstwo dla wtyczki bbPress. System automatycznie cenzuruje wulgaryzmy przed publikacją posta.</p>
                
                <form method="post" class="mt-4 p-3 bg-light rounded">
                    <h5 class="mb-3">Utwórz nowy wątek</h5>
                    <input type="hidden" name="action" value="add_post">
                    <div class="mb-3">
                        <input type="text" name="topic" class="form-control" placeholder="Temat dyskusji..." required>
                    </div>
                    <div class="mb-3">
                        <textarea name="content" class="form-control" rows="3" placeholder="Treść wiadomości (spróbuj napisać zakazane słowo np. kurwa, debil)..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-info text-white"><i class="bi bi-send"></i> Opublikuj na forum</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h4 class="mb-3">Ostatnie wątki</h4>
        <?php if(empty($posts)): ?>
            <div class="alert alert-secondary">Forum jest puste. Bądź pierwszym, który rozpocznie dyskusję!</div>
        <?php else: ?>
            <div class="list-group shadow-sm">
                <?php foreach($posts as $p): ?>
                <div class="list-group-item list-group-item-action p-4">
                    <div class="d-flex w-100 justify-content-between mb-2">
                        <a href="forum_topic.php?id=<?= $p['id'] ?>" class="text-decoration-none">
                            <h5 class="mb-1 text-primary"><?= htmlspecialchars($p['topic']) ?></h5>
                        </a>
                        <small class="text-muted"><i class="bi bi-clock"></i> <?= date('d.m.Y H:i', strtotime($p['created_at'])) ?></small>
                    </div>
                    <p class="mb-2 fs-5">
                        <!-- nl2br() i htmlspecialchars() żeby zabezpieczyć przed XSS, ale pokazać nową linię -->
                        <?= nl2br(htmlspecialchars($p['content'])) ?>
                    </p>
                    <small class="text-muted"><i class="bi bi-person-circle"></i> Autor: <strong><?= htmlspecialchars($p['username']) ?></strong></small>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
