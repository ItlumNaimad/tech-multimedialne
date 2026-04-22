<?php
require_once 'db_connect.php';
require_once 'header.php';

if (!isAdmin()) {
    echo "<div class='alert alert-danger mt-3'>Brak dostępu. Strona tylko dla administratorów.</div>";
    require_once 'footer.php';
    exit;
}

// Akcje administratora
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == 'toggle_ban' && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $stmt = $pdo->prepare("UPDATE users SET is_banned = NOT is_banned WHERE id = ? AND role != 'admin'");
        $stmt->execute([$user_id]);
    }
    elseif ($action == 'delete_user' && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->execute([$user_id]);
    }
    elseif ($action == 'delete_all_posts' && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        $stmt = $pdo->prepare("DELETE FROM posts WHERE created_by = ?");
        $stmt->execute([$user_id]);
    }
    elseif ($action == 'delete_topic' && isset($_POST['topic_id'])) {
        $stmt = $pdo->prepare("DELETE FROM topics WHERE id = ?");
        $stmt->execute([$_POST['topic_id']]);
    }
    elseif ($action == 'delete_thread' && isset($_POST['thread_id'])) {
        $stmt = $pdo->prepare("DELETE FROM threads WHERE id = ?");
        $stmt->execute([$_POST['thread_id']]);
    }
    
    header("Location: admin.php");
    exit;
}

$users = $pdo->query("SELECT id, username, role, is_banned, profanity_count FROM users ORDER BY id")->fetchAll();
$topics = $pdo->query("SELECT id, title FROM topics ORDER BY created_at DESC")->fetchAll();
$threads = $pdo->query("SELECT th.id, th.title, t.title as topic_title FROM threads th JOIN topics t ON th.topic_id = t.id ORDER BY th.created_at DESC")->fetchAll();
?>

<h2>Panel Administratora</h2>

<div class="row mt-4">
    <!-- Użytkownicy -->
    <div class="col-md-12 mb-4">
        <h4>Użytkownicy</h4>
        <table class="table table-bordered table-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th><th>Login</th><th>Rola</th><th>Wulgaryzmy</th><th>Status</th><th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['profanity_count'] ?></td>
                    <td>
                        <?= $u['is_banned'] ? '<span class="text-danger">Zbanowany</span>' : '<span class="text-success">Aktywny</span>' ?>
                    </td>
                    <td>
                        <?php if($u['role'] != 'admin'): ?>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="action" value="toggle_ban">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-sm <?= $u['is_banned'] ? 'btn-success' : 'btn-warning' ?>"><?= $u['is_banned'] ? 'Odbanuj' : 'Zbanuj' ?></button>
                            </form>
                            <form method="post" class="d-inline" onsubmit="return confirm('Usunąć tego użytkownika całkowicie?');">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Usuń użytkownika</button>
                            </form>
                            <form method="post" class="d-inline" onsubmit="return confirm('Usunąć WSZYSTKIE posty tego użytkownika?');">
                                <input type="hidden" name="action" value="delete_all_posts">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Usuń posty</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Tematy -->
    <div class="col-md-6 mb-4">
        <h4>Tematy</h4>
        <ul class="list-group">
            <?php foreach($topics as $t): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($t['title']) ?>
                <form method="post" onsubmit="return confirm('Usunąć ten temat i wszystko w nim?');">
                    <input type="hidden" name="action" value="delete_topic">
                    <input type="hidden" name="topic_id" value="<?= $t['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Usuń</button>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Wątki -->
    <div class="col-md-6 mb-4">
        <h4>Wątki</h4>
        <ul class="list-group">
            <?php foreach($threads as $th): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><small class="text-muted">[<?= htmlspecialchars($th['topic_title']) ?>]</small><br><?= htmlspecialchars($th['title']) ?></span>
                <form method="post" onsubmit="return confirm('Usunąć ten wątek i jego posty?');">
                    <input type="hidden" name="action" value="delete_thread">
                    <input type="hidden" name="thread_id" value="<?= $th['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Usuń</button>
                </form>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>

<?php require_once 'footer.php'; ?>
