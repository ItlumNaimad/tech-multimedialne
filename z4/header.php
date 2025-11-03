<?php declare(strict_types=1); ?>
<header>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top mt-0 mb-0 ms-0 me-0 pt-0 pb-0 ps-0 pe-0">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php?page=home">Z4 Monitor</a>
                <div class="collapse navbar-collapse" id="main_nav">
                    <ul class="navbar-nav me-auto">
                        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                            <li class="nav-item">
                                <a class="nav-link active" href="index.php?page=home">Monitoruj</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=dodaj">Dodaj Host</a>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                                    <?php
                                    $default_avatar = 'media/menu_icons/info.svg';
                                    if (!empty($_SESSION['avatar_path'])) {
                                        $avatar_src = '../' . htmlspecialchars($_SESSION['avatar_path']);
                                    } else { $avatar_src = $default_avatar; }
                                    ?>
                                    <img src="<?php echo $avatar_src; ?>" alt="Avatar" class="rounded-circle" height="24" width="24" style="margin-right: 8px; object-fit: cover;">
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="wyloguj.php">
                                            <i class="bi bi-box-arrow-right me-2"></i>Wyloguj się
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=logowanie">Zaloguj się</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=rejestracja">Zarejestruj się</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main_nav" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
    </div>
</header>