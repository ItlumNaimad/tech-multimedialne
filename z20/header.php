<?php require_once 'auth.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Portal Ogłoszeniowy (z20)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .offer-card img { height: 200px; object-fit: cover; }
        .sidebar { min-height: calc(100vh - 56px); background-color: #f8f9fa; }
        body { background-color: #f4f6f9; }
        .navbar { background-color: #0d6efd !important; }
        .card { border-radius: 10px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php"><i class="bi bi-house-door-fill"></i> Nieruchomości</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <?php if (isLoggedIn()): ?>
            <li class="nav-item"><a class="nav-link text-white fw-bold" href="my_offers.php">Moje Ogłoszenia</a></li>
            <li class="nav-item"><a class="nav-link text-warning fw-bold" href="offer_create.php">Dodaj Ogłoszenie</a></li>
            <li class="nav-item"><span class="nav-link text-light"><strong><?= htmlspecialchars(getCurrentUsername()) ?></strong> <?= isBanned() ? '(Zbanowany)' : '' ?></span></li>
             <?php if (isModerator()): ?>
                <li class="nav-item"><a class="nav-link text-warning" href="admin.php">Panel Admina/Moda</a></li>
             <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Wyloguj</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link text-white" href="login.php">Zaloguj</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="register.php">Zarejestruj</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar kategorii ogłoszeń (lewostronne menu) -->
        <div class="col-md-3 col-lg-2 sidebar p-3 border-end d-none d-md-block bg-white">
            <h5 class="text-primary mb-3"><i class="bi bi-funnel-fill"></i> Kategorie</h5>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action fw-bold text-dark">Wszystkie ogłoszenia</a>
                <a href="index.php?category=1" class="list-group-item list-group-item-action text-primary"><i class="bi bi-building"></i> Mieszkania</a>
                <a href="index.php?category=2" class="list-group-item list-group-item-action text-success"><i class="bi bi-house-fill"></i> Domy</a>
                <a href="index.php?category=3" class="list-group-item list-group-item-action text-warning"><i class="bi bi-signpost-2"></i> Działki budowlane</a>
                <a href="index.php?category=4" class="list-group-item list-group-item-action text-info"><i class="bi bi-tree-fill"></i> Działki ROD</a>
                <?php if (isLoggedIn()): ?>
                <a href="my_offers.php" class="list-group-item list-group-item-action text-primary fw-bold mt-3 border-top"><i class="bi bi-person-fill"></i> Twoje Ogłoszenia</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-md-9 col-lg-10 p-4">

