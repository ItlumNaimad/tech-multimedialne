<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?page=home">
            <i class="bi bi-spotify"></i> mySpotify
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?page=home">Biblioteka</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=upload">Dodaj Utwór</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
        Witaj, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Gość'); ?>
      </span>
            <a href="../z2/wyloguj.php" class="btn btn-danger btn-sm">Wyloguj</a>
        </div>
    </div>
</nav>