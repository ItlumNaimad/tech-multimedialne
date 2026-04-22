<?php
require_once 'db_connect.php';
require_once 'header.php';

if (!isModerator()) {
    echo "<div class='alert alert-danger m-4'>Brak dostępu! Wymagane uprawnienia Moderatora lub Administratora.</div>";
    require_once 'footer.php'; exit;
}

// Obsługa akcji
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $is_admin = isAdmin();
    
    // Uzytkownicy
    if ($action == 'toggle_ban' && isset($_POST['user_id'])) {
        $u_id = $_POST['user_id'];
        // Admin nie może zbanować admina z poziomu tego skryptu
        $stmt = $pdo->prepare("UPDATE users SET is_banned = NOT is_banned WHERE id = ? AND role != 'admin'");
        if (!$is_admin) {
            $stmt = $pdo->prepare("UPDATE users SET is_banned = NOT is_banned WHERE id = ? AND role = 'user'"); // mod tylko zwyklych
        }
        $stmt->execute([$u_id]);
    }
    elseif ($action == 'delete_user' && isset($_POST['user_id'])) {
        $u_id = $_POST['user_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        if (!$is_admin) {
             $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        }
        $stmt->execute([$u_id]);
    }
    elseif ($action == 'make_mod' && isset($_POST['user_id']) && $is_admin) {
        $stmt = $pdo->prepare("UPDATE users SET role = 'moderator' WHERE id = ? AND role = 'user'");
        $stmt->execute([$_POST['user_id']]);
    }
    elseif ($action == 'remove_mod' && isset($_POST['user_id']) && $is_admin) {
        $stmt = $pdo->prepare("UPDATE users SET role = 'user' WHERE id = ? AND role = 'moderator'");
        $stmt->execute([$_POST['user_id']]);
    }
    
    // Galerie
    elseif ($action == 'delete_gallery' && isset($_POST['gallery_id'])) {
        $stmt = $pdo->prepare("DELETE FROM galleries WHERE id = ?");
        $stmt->execute([$_POST['gallery_id']]);
    }
    
    header("Location: admin.php"); exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY role, id")->fetchAll();
$galleries = $pdo->query("SELECT g.*, u.username as owner_name FROM galleries g JOIN users u ON g.user_id = u.id ORDER BY g.created_at DESC")->fetchAll();
?>

<div class="p-3">
    <h2 class="mb-4">Panel Administracyjny <small class="text-muted">(<?= isAdmin() ? 'Administrator' : 'Moderator' ?>)</small></h2>

    <h4>Zarządzanie kontami</h4>
    <div class="table-responsive mb-5">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr><th>ID</th><th>Username</th><th>Rola</th><th>Status</th><th>Akcje</th></tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                            <span class="badge bg-<?= $u['role']=='admin' ? 'danger' : ($u['role']=='moderator' ? 'warning text-dark' : 'secondary') ?>">
                                <?= $u['role'] ?>
                            </span>
                        </td>
                        <td><?= $u['is_banned'] ? '<span class="text-danger fw-bold">ZBANOWANY</span>' : 'Aktywny' ?></td>
                        <td>
                            <?php 
                            // Logika wyświetlania
                            $my_role_ok = ($u['role'] == 'user') || (isAdmin() && $u['role'] == 'moderator');
                            ?>
                            <?php if ($my_role_ok): ?>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="action" value="toggle_ban"><input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button class="btn btn-sm <?= $u['is_banned'] ? 'btn-success' : 'btn-dark' ?>"><?= $u['is_banned'] ? 'Odblokuj' : 'Blokuj (Ban)' ?></button>
                                </form>
                                <form method="post" class="d-inline" onsubmit="return confirm('Konto zniknie permanentnie!');">
                                    <input type="hidden" name="action" value="delete_user"><input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                    <button class="btn btn-sm btn-outline-danger">Usuń konto</button>
                                </form>
                                <?php if (isAdmin() && $u['role'] == 'user'): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="make_mod"><input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button class="btn btn-sm btn-outline-warning">Nadaj Moderatora</button>
                                    </form>
                                <?php elseif (isAdmin() && $u['role'] == 'moderator'): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="action" value="remove_mod"><input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button class="btn btn-sm btn-warning">Zabierz Moderatora</button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <h4>Zarządzanie wszystkimi Galeriami</h4>
    <div class="row row-cols-1 row-cols-md-2 g-3 mt-1">
        <?php foreach($galleries as $g): ?>
            <div class="col">
                <div class="card p-3 shadow-sm border-0 bg-light">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="m-0 mb-1"><?= htmlspecialchars($g['title']) ?></h5>
                            Właściciel: <strong><?= htmlspecialchars($g['owner_name']) ?></strong> <br>
                            Widoczność: <?= $g['visibility'] ?> | Typ folderu: <?= $g['folder_name'] ?>
                        </div>
                        <form method="post" onsubmit="return confirm('Usunąć galerię wraz z całą zwartością?');">
                            <input type="hidden" name="action" value="delete_gallery">
                            <input type="hidden" name="gallery_id" value="<?= $g['id'] ?>">
                            <button class="btn btn-danger"><i class="bi bi-trash"></i> Całkowite Usunięcie</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
