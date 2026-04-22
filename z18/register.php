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
    
    if (empty($username) || empty($password)) {
        $error = "Wypełnij wszystkie pola.";
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
                // Tworzenie katalogu macierzystego użytkownika
                $userDir = __DIR__ . '/uploads/' . $username;
                if (!is_dir($userDir)) {
                    mkdir($userDir, 0777, true);
                }
                $success = "Konto zostało założone. Katalog $username utworzony. Możesz zapukać do Drzwi Logowania!";
            } else {
                $error = "Błąd systemu podczas rejestracji.";
            }
        }
    }
}
?>
<h2>Rejestracja w Photo Gallery</h2>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="login.php">Zaloguj się!</a></div><?php endif; ?>

<form method="post" class="mt-4" style="max-width: 400px;">
    <div class="mb-3">
        <label class="form-label">Nazwa użytkownika (Login)</label>
        <input type="text" name="username" class="form-control" required>
        <div class="form-text">Posłuży do utworzenia przestrzeni na Twoje foldery.</div>
    </div>
    <div class="mb-3">
        <label class="form-label">Hasło</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary d-block w-100"><i class="bi bi-person-plus"></i> Zarejestruj</button>
</form>

<?php require_once 'footer.php'; ?>
