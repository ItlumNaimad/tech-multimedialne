<?php
require_once 'db_connect.php';
require_once 'header.php';
require_once 'SecurityHelper.php';
updateSessionUser($pdo, getCurrentUserId());

$thread_id = $_GET['id'] ?? $_POST['thread_id'] ?? null;
if (!$thread_id) {
    if (isset($_POST['ajax'])) {
        echo json_encode(['error' => 'Nie podano ID wątku.']);
        exit;
    }
    echo "Nie podano ID wątku.";
    require_once 'footer.php';
    exit;
}

// Fetch thread info
$stmt = $pdo->prepare("SELECT th.*, t.id as topic_id, t.title as topic_title FROM threads th JOIN topics t ON th.topic_id = t.id WHERE th.id = ?");
$stmt->execute([$thread_id]);
$thread = $stmt->fetch();

if (!$thread) {
    if (isset($_POST['ajax'])) {
        echo json_encode(['error' => 'Wątek nie istnieje.']);
        exit;
    }
    echo "Wątek nie istnieje.";
    require_once 'footer.php';
    exit;
}

// AJAX post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');

    if (!isLoggedIn()) {
        echo json_encode(['error' => 'Musisz być zalogowany.']);
        exit;
    }
    if (isBanned()) {
        echo json_encode(['error' => 'Zostałeś zbanowany i nie możesz dodawać odpowiedzi.', 'banned' => true]);
        exit;
    }

    $content = trim($_POST['content'] ?? '');
    $user_id = getCurrentUserId();
    $username = getCurrentUsername();
    
    if (empty($content)) {
        echo json_encode(['error' => 'Wpisz treść wiadomości.']);
        exit;
    }

    // 1. Sprawdzanie wulgaryzmów
    if (SecurityHelper::hasProfanity($content)) {
        $new_count = ($_SESSION['profanity_count'] ?? 0) + 1;
        $stmt = $pdo->prepare("UPDATE users SET profanity_count = ? WHERE id = ?");
        $stmt->execute([$new_count, $user_id]);
        $_SESSION['profanity_count'] = $new_count;
        
        $now = date('Y-m-d H:i');
        if ($new_count == 1) {
            $sys_msg = "$now Usunięto post użytkownika $username, ze względu na użyty wulgaryzm. Jest to oficjalne ostrzeżenie dla użytkownika $username, przy kolejnym użytym wulgaryzmie użytkownik ten zostanie zabanowany.";
            $v_stmt = $pdo->prepare("INSERT INTO posts (thread_id, content, created_by, is_system_msg) VALUES (?, ?, ?, 1)");
            $v_stmt->execute([$thread_id, $sys_msg, $user_id]);
            
            echo json_encode(['success' => true, 'system_msg' => true, 'message' => $sys_msg]);
            exit;
        } else {
            $sys_msg = "$now Usunięto post użytkownika $username, ze względu na użyty wulgaryzm. Użytkownik $username został zabanowany z powodu używania wulgaryzmów.";
            $b_stmt = $pdo->prepare("UPDATE users SET is_banned = 1 WHERE id = ?");
            $b_stmt->execute([$user_id]);
            $_SESSION['is_banned'] = 1;
            
            $v_stmt = $pdo->prepare("INSERT INTO posts (thread_id, content, created_by, is_system_msg) VALUES (?, ?, ?, 1)");
            $v_stmt->execute([$thread_id, $sys_msg, $user_id]);
            
            echo json_encode(['success' => true, 'banned' => true, 'system_msg' => true, 'message' => $sys_msg]);
            exit;
        }
    } else {
        // Filtrowanie linków
        $content = SecurityHelper::filterBadLinks($content);
        
        // Plik
        $media_path = null;
        if (isset($_FILES['media']) && $_FILES['media']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $filename = time() . '_' . basename($_FILES['media']['name']);
            $dest = $upload_dir . $filename;
            if (move_uploaded_file($_FILES['media']['tmp_name'], $dest)) {
                $media_path = 'uploads/' . $filename;
            }
        }
        
        $stmt = $pdo->prepare("INSERT INTO posts (thread_id, content, created_by, media_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$thread_id, htmlspecialchars($content), $user_id, $media_path]);
        $post_id = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'post' => [
                'id' => $post_id,
                'content' => nl2br(htmlspecialchars($content)),
                'username' => $username,
                'created_at' => date('Y-m-d H:i:s'),
                'media_path' => $media_path,
                'can_delete' => true
            ]
        ]);
        exit;
    }
}

// Fetch all posts to display normally
$stmt = $pdo->prepare("SELECT p.*, u.username FROM posts p LEFT JOIN users u ON p.created_by = u.id WHERE p.thread_id = ? ORDER BY p.created_at ASC");
$stmt->execute([$thread_id]);
$posts = $stmt->fetchAll();
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php">Forum</a></li>
    <li class="breadcrumb-item"><a href="topic.php?id=<?= $thread['topic_id'] ?>"><?= htmlspecialchars($thread['topic_title']) ?></a></li>
    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($thread['title']) ?></li>
  </ol>
</nav>

<h2>Wątek: <?= htmlspecialchars($thread['title']) ?></h2>

<div class="mt-4" id="postsContainer">
    <?php foreach ($posts as $post): ?>
        <?php 
            $is_sys = $post['is_system_msg']; 
            $can_delete = (!$is_sys && isLoggedIn() && getCurrentUserId() == $post['created_by']) || isAdmin();
        ?>
        <div class="card <?= $is_sys ? 'admin-post' : 'post-card' ?>" id="post-<?= $post['id'] ?>">
            <div class="card-header d-flex justify-content-between align-items-center <?= $is_sys ? 'bg-danger text-white' : '' ?>">
                <span>
                    <strong><?= $is_sys ? 'System' : htmlspecialchars($post['username'] ?? 'Nieznany') ?></strong> 
                    <small class="text-muted" <?= $is_sys ? 'style="color:#fff !important;"' : '' ?>><?= $post['created_at'] ?></small>
                </span>
                <?php if ($can_delete): ?>
                    <form action="post_delete.php" method="POST" class="m-0 p-0" onsubmit="return confirm('Napewno usunąć ten post?');">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń post">🗑️ Usuń</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <p class="card-text <?= $is_sys ? 'system-msg' : '' ?>">
                    <?= nl2br($is_sys ? htmlspecialchars($post['content']) : $post['content']) ?>
                </p>
                <?php if (!empty($post['media_path'])): ?>
                    <div class="mt-3">
                        <img src="<?= htmlspecialchars($post['media_path']) ?>" alt="Zalacznik" class="img-fluid" style="max-height: 300px;">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if(empty($posts)): ?>
        <p id="noPostsMsg">Brak odpowiedzi w tym wątku.</p>
    <?php endif; ?>
</div>

<div id="banAlert" class="alert alert-danger mt-4" style="display: <?= isBanned() ? 'block' : 'none' ?>;">
    Zostałeś zbanowany i nie możesz dodawać odpowiedzi.
</div>

<?php if (isLoggedIn() && !isBanned()): ?>
    <div class="card mt-4" id="replyFormCard">
        <div class="card-body">
            <h5 class="card-title">Dodaj odpowiedź</h5>
            <form id="ajaxReplyForm" enctype="multipart/form-data">
                <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
                <input type="hidden" name="ajax" value="1">
                
                <div class="mb-3">
                    <textarea name="content" id="replyContent" class="form-control" rows="4" placeholder="Twoja odpowiedź..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Załącz plik multimedialny (opcjonalnie)</label>
                    <input class="form-control" type="file" name="media" id="replyMedia" accept="image/*,video/*">
                </div>
                <button type="submit" class="btn btn-success" id="replyBtn">Wyślij odpowiedź</button>
                <div id="replyError" class="text-danger mt-2"></div>
            </form>
        </div>
    </div>
<?php elseif(!isLoggedIn()): ?>
    <div class="alert alert-info mt-4">Musisz się <a href="login.php">zalogować</a>, aby odpowiedzieć w wątku.</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('ajaxReplyForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('replyBtn');
        const errDiv = document.getElementById('replyError');
        const container = document.getElementById('postsContainer');
        const noPostsMsg = document.getElementById('noPostsMsg');
        
        btn.disabled = true;
        errDiv.textContent = '';
        
        const formData = new FormData(form);

        fetch('thread.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            
            if (data.error) {
                errDiv.textContent = data.error;
                if (data.banned) {
                    document.getElementById('replyFormCard').style.display = 'none';
                    document.getElementById('banAlert').style.display = 'block';
                }
                return;
            }
            
            if (data.success) {
                if (noPostsMsg) noPostsMsg.style.display = 'none';
                form.reset();
                
                let html = '';
                if (data.system_msg) {
                    html = `
                    <div class="card admin-post">
                        <div class="card-header d-flex justify-content-between align-items-center bg-danger text-white">
                            <span><strong>System</strong> <small class="text-muted" style="color:#fff !important;">Teraz</small></span>
                        </div>
                        <div class="card-body">
                            <p class="card-text system-msg">${data.message}</p>
                        </div>
                    </div>`;
                    
                    if (data.banned) {
                        document.getElementById('replyFormCard').style.display = 'none';
                        document.getElementById('banAlert').style.display = 'block';
                    }
                } else {
                    let mediaHtml = '';
                    if (data.post.media_path) {
                        mediaHtml = `<div class="mt-3"><img src="${data.post.media_path}" alt="Zalacznik" class="img-fluid" style="max-height: 300px;"></div>`;
                    }
                    html = `
                    <div class="card post-card" id="post-${data.post.id}">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>
                                <strong>${data.post.username}</strong> 
                                <small class="text-muted">${data.post.created_at}</small>
                            </span>
                            <form action="post_delete.php" method="POST" class="m-0 p-0" onsubmit="return confirm('Napewno usunąć ten post?');">
                                <input type="hidden" name="post_id" value="${data.post.id}">
                                <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Usuń post">🗑️ Usuń</button>
                            </form>
                        </div>
                        <div class="card-body">
                            <p class="card-text">${data.post.content}</p>
                            ${mediaHtml}
                        </div>
                    </div>`;
                }
                
                container.insertAdjacentHTML('beforeend', html);
                window.scrollTo(0, document.body.scrollHeight);
            }
        })
        .catch(err => {
            btn.disabled = false;
            errDiv.textContent = 'Wystąpił błąd po stronie serwera/sieci.';
            console.error(err);
        });
    });
});
</script>

<?php require_once 'footer.php'; ?>
