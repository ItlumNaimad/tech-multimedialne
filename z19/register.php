<?php
require_once 'auth.php';
require_once 'db_connect.php';

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
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Login może zawierać tylko litery, cyfry i podkreślenia.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Użytkownik o takiej nazwie już istnieje.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
            if ($stmt->execute([$username, $hash])) {
                $success = "Konto zostało pomyślnie założone. Możesz się teraz zalogować!";
            } else {
                $error = "Błąd systemu podczas rejestracji.";
            }
        }
    }
}

require_once 'header.php';
?>
<h2>Rejestracja w Portalu Firmowym</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="login.php">Zaloguj się!</a></div><?php endif; ?>

<form method="post" class="mt-4" style="max-width: 400px;">
    <div class="mb-3">
        <label class="form-label">Nazwa użytkownika (Login)</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Hasło</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Powtórz hasło</label>
        <input type="password" name="password_confirm" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary d-block w-100"><i class="bi bi-person-plus"></i> Zarejestruj</button>
</form>

<?php require_once 'footer.php'; ?>
