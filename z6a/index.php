<?php
session_start();
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
    <title>mySpotify</title>
    <link rel="stylesheet" href="css/twoj_css.css">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Custom dark theme -->
    <style>
        body {
            background-color: #121212;
            color: #fff;
        }
        .card {
            background-color: #282828;
        }
        .list-group-item {
            background-color: #282828;
            border-color: #404040;
        }
        .form-control {
            background-color: #333;
            border-color: #555;
            color: #fff;
        }
        .form-control:focus {
            background-color: #333;
            border-color: #1db954;
            color: #fff;
            box-shadow: none;
        }
        .form-range::-webkit-slider-thumb {
            background-color: #1db954;
        }
        .form-range::-moz-range-thumb {
            background-color: #1db954;
        }
    </style>
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

<footer class="mt-auto">
    <?php if (isset($_SESSION['loggedin'])): ?>
        <?php include 'footer.php'; ?>
    <?php endif; ?>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>