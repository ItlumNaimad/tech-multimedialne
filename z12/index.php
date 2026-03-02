<?php
session_start();

// --- OCHRONA SESJI (Izolacja od z6a/z6b) ---
if (isset($_SESSION['loggedin']) && ($_SESSION['app_id'] ?? '') !== 'lab12') {
    session_unset();
    session_destroy();
    session_start();
}
$page = $_GET['page'] ?? 'home';

// Prosty router
$page = $_GET['page'] ?? 'home';
$allowed = ['home', 'logowanie', 'rejestracja'];
if (!in_array($page, $allowed)) $page = 'home';

// Przekierowanie niezalogowanych
if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) && !in_array($page, ['logowanie', 'rejestracja'])) {
    header('Location: index.php?page=logowanie');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab 12 - System Logowania</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="d-flex flex-column h-100">

<?php if (isset($_SESSION['loggedin'])): ?>
    <?php include 'header.php'; ?>
<?php endif; ?>

<main class="container py-4 flex-shrink-0">
    <?php
    $file = "pages/$page.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<h2 class='text-danger'>Plik nie istnieje: $file</h2>";
    }
    ?>
</main>

<footer class="footer mt-auto py-3 bg-light border-top">
    <div class="container text-center">
        <span class="text-muted">Laboratorium 12 &copy; 2026</span>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>