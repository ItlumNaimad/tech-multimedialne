<?php
require_once 'db_connect.php';
require_once 'header.php';
updateSessionUser($pdo, getCurrentUserId());

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn() && !isBanned()) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO topics (title, created_by) VALUES (?, ?)");
        $stmt->execute([$title, getCurrentUserId()]);
        header("Location: index.php");
        exit;
    }
}

$stmt = $pdo->query("SELECT t.*, u.username FROM topics t LEFT JOIN users u ON t.created_by = u.id ORDER BY t.created_at DESC");
$topics = $stmt->fetchAll();
?>
<h2>Tematy na forum</h2>

<?php if (isLoggedIn() && !isBanned()): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Utwórz nowy temat</h5>
            <form method="post">
                <div class="input-group">
                    <input type="text" name="title" class="form-control" placeholder="Tytuł tematu..." required>
                    <button type="submit" class="btn btn-primary">Dodaj</button>
                </div>
            </form>
        </div>
    </div>
<?php elseif (isBanned()): ?>
    <div class="alert alert-danger">Zostałeś zbanowany i nie możesz dodawać nowych tematów.</div>
<?php endif; ?>

<div class="list-group">
    <?php foreach ($topics as $topic): ?>
        <a href="topic.php?id=<?= $topic['id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1"><?= htmlspecialchars($topic['title']) ?></h5>
                <small>Autor: <?= htmlspecialchars($topic['username'] ?? 'Nieznany') ?> | <?= $topic['created_at'] ?></small>
            </div>
            <span class="badge bg-secondary rounded-pill">Wątki</span>
        </a>
    <?php endforeach; ?>
    <?php if(empty($topics)): ?>
        Brak tematów na forum.
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
