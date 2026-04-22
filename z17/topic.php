<?php
require_once 'db_connect.php';
require_once 'header.php';
updateSessionUser($pdo, getCurrentUserId());

$topic_id = $_GET['id'] ?? null;
if (!$topic_id) {
    echo "Nie podano ID tematu.";
    require_once 'footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM topics WHERE id = ?");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch();

if (!$topic) {
    echo "Temat nie istnieje.";
    require_once 'footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn() && !isBanned()) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO threads (topic_id, title, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$topic_id, $title, getCurrentUserId()]);
        header("Location: topic.php?id=" . $topic_id);
        exit;
    }
}

$stmt = $pdo->prepare("SELECT th.*, u.username FROM threads th LEFT JOIN users u ON th.created_by = u.id WHERE th.topic_id = ? ORDER BY th.created_at DESC");
$stmt->execute([$topic_id]);
$threads = $stmt->fetchAll();
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php">Forum</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($topic['title']) ?></li>
  </ol>
</nav>

<h2>Wątki w temacie: <?= htmlspecialchars($topic['title']) ?></h2>

<?php if (isLoggedIn() && !isBanned()): ?>
    <div class="card mb-4 mt-3">
        <div class="card-body">
            <h5 class="card-title">Nowy wątek</h5>
            <form method="post">
                <div class="input-group">
                    <input type="text" name="title" class="form-control" placeholder="Tytuł wątku..." required>
                    <button type="submit" class="btn btn-primary">Utwórz</button>
                </div>
            </form>
        </div>
    </div>
<?php elseif (isBanned()): ?>
    <div class="alert alert-danger mt-3">Zostałeś zbanowany i nie możesz dodawać nowych wątków.</div>
<?php endif; ?>

<div class="list-group mt-3">
    <?php foreach ($threads as $thread): ?>
        <a href="thread.php?id=<?= $thread['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><?= htmlspecialchars($thread['title']) ?></h5>
                <small>Autor: <?= htmlspecialchars($thread['username'] ?? 'Nieznany') ?> | <?= $thread['created_at'] ?></small>
            </div>
            <span class="badge bg-primary rounded-pill">Odpowiedzi</span>
        </a>
    <?php endforeach; ?>
    <?php if(empty($threads)): ?>
        <p class="mt-3">Brak wątków w tym temacie.</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
