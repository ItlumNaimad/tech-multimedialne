<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$currentUser = $_SESSION['username'];

// Get all users except current one
$users_result = mysqli_query($conn, "SELECT username FROM users WHERE username != '$currentUser'");
$users = [];
while ($row = mysqli_fetch_assoc($users_result)) {
    $users[] = $row['username'];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Komunikator - Wybierz rozmówcę</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Komunikator</a>
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3 text-white">Zalogowany jako: <strong><?php echo $currentUser; ?></strong></span>
            <a class="btn btn-outline-light btn-sm" href="logout.php">Wyloguj</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Dostępni użytkownicy</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (empty($users)): ?>
                        <div class="list-group-item text-center py-4">
                            <p class="text-muted mb-0">Brak innych użytkowników w systemie.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <a href="chat.php?recipient=<?php echo urlencode($user); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <h6 class="mb-0"><?php echo $user; ?></h6>
                                    <small class="text-muted">Kliknij, aby rozpocząć czat</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">Czat</span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer mt-auto py-3 bg-white border-top">
    <div class="container text-center">
        <span class="text-muted">Komunikator &copy; 2026 | Projekt: Techniki Multimedialne</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>