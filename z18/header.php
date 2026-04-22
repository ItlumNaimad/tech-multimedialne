<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Photo Gallery (z18)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .gallery-card img { height: 200px; object-fit: cover; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #f8f9fa; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="bi bi-camera"></i> Photo Gallery</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <?php if (isLoggedIn()): ?>
            <li class="nav-item"><a class="nav-link text-info" href="my_galleries.php">Moje Galerie</a></li>
            <li class="nav-item"><span class="nav-link text-light"><strong><?= htmlspecialchars(getCurrentUsername()) ?></strong> <?= isBanned() ? '(Zbanowany)' : '' ?></span></li>
             <?php if (isModerator()): ?>
                <li class="nav-item"><a class="nav-link text-warning" href="admin.php">Panel Admina/Moda</a></li>
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

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar kategorii galerii (lewostronny menu) -->
        <div class="col-md-3 col-lg-2 sidebar p-3 border-end d-none d-md-block">
            <h5>Wszystkie galerie</h5>
            <div class="list-group">
                <a href="index.php" class="list-group-item list-group-item-action">Wszystkie publiczne</a>
                <!-- Użytkownik widzi też komercyjne, ale one mają filtry. Dynamicznie je lądujemy w indexie, ale tu skrót -->
                <a href="index.php?type=public" class="list-group-item list-group-item-action text-success">Zwykłe (Publiczne)</a>
                <a href="index.php?type=commercial" class="list-group-item list-group-item-action text-warning">Komercyjne (Znak Wodny)</a>
                <?php if (isLoggedIn()): ?>
                <a href="my_galleries.php" class="list-group-item list-group-item-action text-primary mt-3">Przejdź do Twoich</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4">
