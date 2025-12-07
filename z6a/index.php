<?php
session_start();
// Prosty router
$page = $_GET['page'] ?? 'home';
$allowed = ['home', 'upload', 'logowanie', 'rejestracja'];
if (!in_array($page, $allowed)) $page = 'home';

// Przekierowanie niezalogowanych
if ((!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) && !in_array($page, ['logowanie', 'rejestracja'])) {
    header('Location: index.php?page=logowanie');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mySpotify</title>
    <link href="css/output.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="h-full text-white font-sans antialiased">

<?php if (isset($_SESSION['loggedin'])): ?>
    <?php include 'header.php'; ?>
<?php endif; ?>

<main class="container mx-auto px-4 py-6">
    <?php
    $file = "pages/$page.php";
    if (file_exists($file)) include $file;
    else echo "<h2 class='text-red-500'>Plik nie istnieje: $file</h2>";
    ?>
</main>

</body>
</html>