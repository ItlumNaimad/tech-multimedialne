<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $title = trim($_POST['title'] ?? '');
        $assigned_to = (int)($_POST['assigned_to'] ?? 0);
        
        if (!empty($title) && $assigned_to > 0) {
            $stmt = $pdo->prepare("INSERT INTO todo_tasks (title, assigned_to, created_by) VALUES (?, ?, ?)");
            $stmt->execute([$title, $assigned_to, getCurrentUserId()]);
        }
    } elseif ($_POST['action'] === 'complete') {
        $task_id = (int)($_POST['task_id'] ?? 0);
        if ($task_id > 0) {
            // Tylko przypisany użytkownik lub admin może oznaczyć zadanie
            $stmt = $pdo->prepare("UPDATE todo_tasks SET status = 'done' WHERE id = ? AND (assigned_to = ? OR ? = 1)");
            $stmt->execute([$task_id, getCurrentUserId(), isAdmin() ? 1 : 0]);
        }
    }
    header("Location: todo.php");
    exit;
}

// Pobieranie zadań
$stmt = $pdo->query("SELECT t.*, u1.username as assigned_user, u2.username as creator 
                     FROM todo_tasks t 
                     JOIN users u1 ON t.assigned_to = u1.id 
                     JOIN users u2 ON t.created_by = u2.id 
                     ORDER BY t.created_at DESC");
$tasks = $stmt->fetchAll();

// Pobieranie userów do selecta
$users = $pdo->query("SELECT id, username FROM users")->fetchAll();

require_once 'header.php';
?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h3 class="card-title text-primary"><i class="bi bi-check2-square"></i> System ToDo (Zarządzanie Zadaniami)</h3>
        <p class="text-muted">Plugin zastępujący WP Project Manager. Rozliczaj pracowników z wykonanych zadań.</p>
        
        <form method="post" class="row g-3 mt-3 border-top pt-3">
            <input type="hidden" name="action" value="add">
            <div class="col-md-6">
                <input type="text" name="title" class="form-control" placeholder="Treść nowego zadania..." required>
            </div>
            <div class="col-md-4">
                <select name="assigned_to" class="form-select" required>
                    <option value="">-- Przypisz do użytkownika --</option>
                    <?php foreach($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus"></i> Dodaj</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <?php foreach($tasks as $t): ?>
    <div class="col-md-4 mb-3">
        <div class="card <?= $t['status'] === 'done' ? 'bg-light border-success' : 'border-warning' ?>">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($t['title']) ?></h5>
                <p class="card-text small text-muted">
                    Przypisane do: <strong><?= htmlspecialchars($t['assigned_user']) ?></strong><br>
                    Zlecone przez: <?= htmlspecialchars($t['creator']) ?><br>
                    Status: <?= $t['status'] === 'done' ? '<span class="badge bg-success">Wykonane</span>' : '<span class="badge bg-warning text-dark">Oczekujące</span>' ?>
                </p>
                <?php if($t['status'] === 'pending'): ?>
                <form method="post">
                    <input type="hidden" name="action" value="complete">
                    <input type="hidden" name="task_id" value="<?= $t['id'] ?>">
                    <button class="btn btn-sm btn-success w-100"><i class="bi bi-check-circle"></i> Oznacz jako zrobione</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>
