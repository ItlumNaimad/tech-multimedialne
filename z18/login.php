<?php
require_once 'db_connect.php';
require_once 'header.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['is_banned'] = $user['is_banned'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Błędny login lub hasło.";
    }
}
?>
<h2>Logowanie</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<form method="post" class="mt-4" style="max-width: 400px;">
    <div class="mb-3">
        <label class="form-label">Login</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Hasło</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success d-block w-100"><i class="bi bi-box-arrow-in-right"></i> Zaloguj</button>
</form>

<?php require_once 'footer.php'; ?>
