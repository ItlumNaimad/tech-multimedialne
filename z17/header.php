<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Forum Dyskusyjne z17</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .banned-msg { color: red; font-weight: bold; }
        .system-msg { color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 5px; font-style: italic; }
        .post-card { border-left: 4px solid #0d6efd; margin-bottom: 15px; }
        .admin-post { border-left: 4px solid #dc3545; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Forum PBŚ</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isLoggedIn()): ?>
            <li class="nav-item"><span class="nav-link text-light">Zalogowany jako: <strong><?= htmlspecialchars(getCurrentUsername()) ?></strong> <?= isBanned() ? '<span class="text-danger">(Zbanowany)</span>' : '' ?></span></li>
            <?php if (isAdmin()): ?>
                <li class="nav-item"><a class="nav-link text-warning" href="admin.php">Panel Admina</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Wyloguj</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Zaloguj</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Zarejestruj</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container mt-4">
