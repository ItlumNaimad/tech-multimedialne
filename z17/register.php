<?php
require_once 'db_connect.php';
require_once 'header.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);
    
    if (empty($username) || empty($password) || empty($password_confirm)) {
        $error = "Wypełnij wszystkie pola.";
    } elseif ($password !== $password_confirm) {
        $error = "Hasła nie są identyczne.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Użytkownik o takiej nazwie już istnieje.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            if ($stmt->execute([$username, $hash])) {
                $success = "Konto zostało założone. Możesz się teraz zalogować.";
            } else {
                $error = "Błąd podczas rejestracji.";
            }
        }
    }
}
?>
<h2>Rejestracja</h2>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
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
    <div class="mb-3">
        <label>Powtórz hasło</label>
        <input type="password" name="password_confirm" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Zarejestruj</button>
</form>

<?php require_once 'footer.php'; ?>
