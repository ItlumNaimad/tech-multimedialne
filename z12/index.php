<?php
/**
 * Plik: index.php
 * Cel: Główny kontroler (router) aplikacji Laboratorium 12.
 * Funkcjonalność: Zarządza strukturą strony, sesją i dostępem do podstron.
 * Wykorzystane biblioteki: Bootstrap 5 (CSS/JS).
 * Sposób działania: 
 *   - Izoluje sesję lab12 od innych zadań.
 *   - Obsługuje parametr 'page' w adresie URL, ładując odpowiednie pliki z katalogu pages/.
 *   - Wymusza logowanie dla zastrzeżonych sekcji aplikacji.
 */
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
$allowed = ['home', 'logowanie', 'rejestracja', 'scada', 'stats', 'formularz'];
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
    
    <!-- FontAwesome 6 (Ikony i Animacje) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Banner Cookies -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
      <div id="cookieToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
        <div class="toast-header bg-primary text-white">
          <i class="bi bi-cookie me-2"></i>
          <strong class="me-auto">Polityka Cookies</strong>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-white">
          Ta aplikacja wykorzystuje pliki cookies do celów analitycznych (tracker.js). Czy wyrażasz zgodę?
          <div class="mt-2 pt-2 border-top">
            <button type="button" class="btn btn-primary btn-sm" id="acceptCookies">Akceptuję</button>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="toast">Odrzucam</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Skrypt analityczny Tracker.js -->
    <script src="tracker.js"></script>
    <?php if ($page === 'scada'): ?>
        <!-- Biblioteki Wykresów dla SCADA -->
        <script src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php endif; ?>
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
        <span class="text-muted">Laboratorium 12 Technologie Multimedialne</span>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>