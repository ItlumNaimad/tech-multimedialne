<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zbijanie Bąków S.A. – Portal Firmowy (z19)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .navbar { background-color: #0d6efd !important; }
        .card { border-radius: 10px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="index.php"><i class="bi bi-briefcase-fill"></i> Zbijanie Bąków S.A.</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">O firmie</a></li>
          <li class="nav-item"><a class="nav-link" href="kontakt.php">Kontakt</a></li>
          <?php if (isLoggedIn()): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Moduły</a>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="todo.php"><i class="bi bi-check2-square"></i> System ToDo</a></li>
                <li><a class="dropdown-item" href="elearning.php"><i class="bi bi-mortarboard"></i> e-Learning</a></li>
                <li><a class="dropdown-item" href="crm.php"><i class="bi bi-headset"></i> CRM</a></li>
                <li><a class="dropdown-item" href="forum.php"><i class="bi bi-chat-text"></i> Forum</a></li>
                <li><a class="dropdown-item" href="gallery.php"><i class="bi bi-images"></i> Galeria</a></li>
            </ul>
          </li>
          <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if (isLoggedIn()): ?>
            <li class="nav-item"><span class="nav-link text-light"><i class="bi bi-person-circle"></i> <strong><?= htmlspecialchars(getCurrentUsername()) ?></strong> (<?= htmlspecialchars(getUserRole()) ?>)</span></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Wyloguj</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link text-white" href="login.php">Zaloguj</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="register.php">Zarejestruj</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
