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
        $_SESSION['profanity_count'] = $user['profanity_count'];
        
        header("Location: index.php");
        exit;
    } else {
        $error = "Błędna nazwa użytkownika lub hasło.";
    }
}
?>
<h2>Logowanie</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post" class="mt-3" style="max-width: 400px;">
    <div class="mb-3">
        <label>Nazwa użytkownika</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Hasło</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Zaloguj</button>
</form>

<?php require_once 'footer.php'; ?>
