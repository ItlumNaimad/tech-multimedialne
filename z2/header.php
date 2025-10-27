<?php declare(strict_types=1);  /* Ta linia musi być pierwsza */ ?>
<header>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top mt-0 mb-0 ms-0 me-0 pt-0 pb-0 ps-0 pe-0">
            <div class="container-fluid">

                <div class="collapse navbar-collapse" id="main_nav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=home">Strona Główna</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 1.x</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=p1_1"> <img src="media/menu_icons/info.svg" height="18"> Polecenie 1.1 </a></li>
                                <li><a class="dropdown-item" href="index.php?page=p1_2"> <img src="media/menu_icons/help.svg" height="18"> Polecenie 1.2 </a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 2.x</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=p2_1"> Polecenie 2.1 </a></li>
                                <li><a class="dropdown-item" href="index.php?page=p2_2"> Polecenie 2.2 </a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Polecenia 3.x</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="index.php?page=p3_1"> Polecenie 3.1 </a></li>
                                <li><a class="dropdown-item" href="index.php?page=p3_2"> Polecenie 3.2 </a></li>
                                <li><a class="dropdown-item" href="index.php?page=p3_3"> Polecenie 3.3 </a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <?php
                        // Sprawdzamy, czy sesja istnieje i czy użytkownik jest zalogowany
                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true):
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                                    <?php
                                        // Ustaw domyślny awatar
                                        $default_avatar = 'media/menu_icons/info.svg'; // Domyślna ikona z szablonu [cite: 61-62]

                                        // Sprawdź, czy użytkownik ma własny awatar i czy nie jest pusty
                                        if (!empty($_SESSION['avatar_path'])) {
                                            // Ścieżka z bazy jest względna do root (np. 'uploads/plik.jpg')
                                            // A my jesteśmy w folderze 'z2', więc musimy dodać '../'
                                            $avatar_src = '../' . htmlspecialchars($_SESSION['avatar_path']);
                                        } else {
                                            $avatar_src = $default_avatar;
                                        }
                                    ?>
                                    <img src="<?php echo $avatar_src; ?>" alt="Avatar" class="rounded-circle" height="24" width="24" style="margin-right: 8px; object-fit: cover;">
                                    <?php echo htmlspecialchars($_SESSION['username']); ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="index.php?page=panel">Mój Panel</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="wyloguj.php">
                                            <i class="bi bi-box-arrow-right me-2"></i>Wyloguj się
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="wyloguj.php">
                                    <i class="bi bi-box-arrow-right me-2"></i>Wyloguj się
                                </a>
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

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#main_nav"  aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

            </div>
        </nav>

    </div>
</header>