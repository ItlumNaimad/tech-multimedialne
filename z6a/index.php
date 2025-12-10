<?php
session_start();
$page = $_GET['page'] ?? 'home';
// Router
// Dodajemy nowe strony do listy dozwolonych
$allowed = [
        'home',
        'upload',
        'logowanie',
        'rejestracja',
        'my_playlists',     // <--- Nowe
        'create_playlist',  // <--- Nowe
        'add_to_playlist'   // <--- Nowe
];
if (!in_array($page, $allowed)) $page = 'home';

// Przekierowanie niezalogowanych
if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) && !in_array($page, ['logowanie', 'rejestracja'])) {
    header('Location: index.php?page=logowanie');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mySpotify - Bootstrap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/twoj_css.css">
</head>
<body class="bg-dark text-light">

<?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
    <?php include 'header.php'; ?>
<?php endif; ?>

<div class="container mt-4">
    <?php
    $file = "pages/$page.php";
    if (file_exists($file)) include $file;
    else echo "<div class='alert alert-danger'>Plik nie istnieje: $file</div>";
    ?>
</div>

<?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
    <?php include 'footer.php'; ?>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>