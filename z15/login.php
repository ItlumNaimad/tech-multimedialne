<?php
session_start();
require_once 'db_connect.php';
require_once 'functions.php';

$typ_req = $_GET['typ'] ?? 'klient';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $pass = $_POST['pass'];
    
    $stmt = $pdo->prepare("SELECT * FROM uzytkownicy WHERE login = ?");
    $stmt->execute([$login]);
    $user = $stmt->fetch();
    
    if ($user && $user['haslo'] == $pass) { // W celach edukacyjnych hasło w tekście jawnym
        $_SESSION['user'] = $user;
        logLogin($pdo, $user['id']);
        
        if ($user['typ'] == 'klient') header('Location: client.php');
        elseif ($user['typ'] == 'pracownik') header('Location: worker.php');
        elseif ($user['typ'] == 'admin') header('Location: admin.php');
        exit;
    } else {
        $error = 'Błędny login lub hasło!';
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie - WHS CRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center">Zaloguj jako <?= ucfirst($typ_req) ?></h3>
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Login</label>
                            <input type="text" name="login" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hasło</label>
                            <input type="password" name="pass" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Zaloguj</button>
                        <div class="mt-3 text-center">
                            <a href="index.php">Wróć do strony głównej</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
