<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php?page=home">
            <i class="bi bi-gear-fill"></i> Laboratorium 12
        </a>
        
        <a href="../index.php" class="btn btn-outline-light btn-sm me-auto ms-3">
            <i class="bi bi-arrow-left"></i> Lista zadań
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto me-3 align-items-center">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php?page=home">Panel</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="navbar-text text-white">
                    <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username'] ?? 'Gość'); ?>
                </span>
                <a href="wyloguj.php" class="btn btn-danger btn-sm">Wyloguj</a>
            </div>
        </div>
    </div>
</nav>