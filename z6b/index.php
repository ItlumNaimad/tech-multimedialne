<?php
session_start();
// --- OCHRONA SESJI (Izolacja od mySpotify) ---
if (isset($_SESSION['loggedin']) && ($_SESSION['app_id'] ?? '') !== 'mynetflix') {
    session_unset();
    session_destroy();
    session_start();
}
$page = $_GET['page'] ?? 'home';

// Prosty router
$page = $_GET['page'] ?? 'home';
$allowed = ['home', 'upload', 'logowanie', 'rejestracja', 'my_playlists', 'create_playlist', 'add_to_playlist'];
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
    <title>myNetflix</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="css/twoj_css.css">
</head>
<body class="d-flex flex-column h-100">

<?php if (isset($_SESSION['loggedin'])): ?>
    <?php include 'header.php'; ?>
<?php endif; ?>

<main class="container py-4 flex-shrink-0">
    <?php
    $file = "pages/$page.php";
    if (file_exists($file)) include $file;
    else echo "<h2 class='text-danger'>Plik nie istnieje: $file</h2>";
    ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>