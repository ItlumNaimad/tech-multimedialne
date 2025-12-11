<nav class="navbar navbar-expand-lg navbar-dark bg-black mb-4 border-bottom border-danger" style="border-width: 2px !important;">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold text-danger fs-3" href="index.php?page=home" style="letter-spacing: 1px;">
            <i class="bi bi-film"></i> myNetflix
        </a>

        <a href="../index.php" class="btn btn-outline-secondary btn-sm me-auto ms-3 border-0">
            <i class="bi bi-arrow-left"></i> Wróć
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3 align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_GET['page'] ?? '') == 'home' ? 'active fw-bold' : ''; ?>" href="index.php?page=home">Filmy</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_GET['page'] ?? '') == 'upload' ? 'active fw-bold' : ''; ?>" href="index.php?page=upload">Dodaj</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($_GET['page'] ?? '') == 'my_playlists' ? 'active fw-bold' : ''; ?>" href="index.php?page=my_playlists">Moja Lista</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text text-white small">
                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Gość'); ?>
                </span>
                <a href="wyloguj.php" class="btn btn-danger btn-sm fw-bold px-3">Wyloguj</a>
            </div>
        </div>
    </div>
</nav>