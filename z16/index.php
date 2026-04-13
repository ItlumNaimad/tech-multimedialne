<?php
session_start();
require_once 'db_connect.php';

// Dla uproszczenia wybieramy pierwszy dostępny CMS (id=1)
// W wersji rozszerzonej można by wykrywać id_cms na podstawie $_SERVER['HTTP_HOST'] lub podkatalogu
$id_cms = 1;

$stmt = $pdo->prepare("SELECT * FROM cms WHERE id_cms = ?");
$stmt->execute([$id_cms]);
$cms = $stmt->fetch();

if (!$cms) {
    die("Błąd: CMS nie istnieje.");
}

$page = $_GET['page'] ?? 'about';
$is_admin = isset($_SESSION['admin_cms']) && $_SESSION['admin_cms'] == $id_cms;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>CMS - <?php echo htmlspecialchars($cms['url']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #343a40; color: white; padding-top: 20px; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover, .sidebar a.active { color: white; background-color: #495057; }
        .header-logo { background-color: white; padding: 10px; border-bottom: 2px solid #dee2e6; text-align: center; }
        .content-area { padding: 30px; background-color: white; min-height: 80vh; border-radius: 8px; margin-top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .admin-footer { background: #e9ecef; padding: 15px; margin-top: 20px; border-top: 1px solid #ced4da; }
        #chatbot-avatar { width: 150px; height: 150px; border-radius: 50%; border: 3px solid #3498db; }
    </style>
    <?php if ($is_admin): ?>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <?php endif; ?>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Menu -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar">
            <h5 class="px-3 mb-3">Menu</h5>
            <a href="index.php?page=about" class="<?php echo $page == 'about' ? 'active' : ''; ?>">O firmie</a>
            <a href="index.php?page=contact" class="<?php echo $page == 'contact' ? 'active' : ''; ?>">Kontakt</a>
            <a href="index.php?page=map" class="<?php echo $page == 'map' ? 'active' : ''; ?>">Jak do nas dotrzeć</a>
            <a href="index.php?page=offer" class="<?php echo $page == 'offer' ? 'active' : ''; ?>">Oferta</a>
            <a href="index.php?page=chatbot" class="<?php echo $page == 'chatbot' ? 'active' : ''; ?>">Chatbot</a>
            <a href="index.php?page=history" class="<?php echo $page == 'history' ? 'active' : ''; ?>">Historia Chatbota</a>

            <div class="mt-5 px-3">
                <?php if ($is_admin): ?>
                    <p class="text-success small">Zalogowano: <b><?php echo htmlspecialchars($_SESSION['admin_user']); ?></b></p>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger w-100 mt-2">Admin Logout</a>
                <?php else: ?>
                    <hr>
                    <form action="login.php" method="POST">
                        <input type="hidden" name="id_cms" value="<?php echo $id_cms; ?>">
                        <div class="mb-2">
                            <input type="text" name="username" class="form-control form-control-sm" placeholder="Login" required>
                        </div>
                        <div class="mb-2">
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">Admin Login</button>
                    </form>
                <?php endif; ?>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Header with Logo -->
            <div class="header-logo d-flex justify-content-between align-items-center">
                <div style="flex-grow: 1;">
                    <img src="<?php echo htmlspecialchars($cms['logo_file']); ?>?t=<?php echo time(); ?>" alt="Logo Firmy" style="max-height: 50px;">
                </div>
                <div id="google_translate_element"></div>
            </div>

            <script type="text/javascript">
                function googleTranslateElementInit() {
                    new google.translate.TranslateElement({pageLanguage: 'pl', includedLanguages: 'en,de,fr'}, 'google_translate_element');
                }
            </script>
            <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

            <div class="content-area">
                <?php if ($is_admin && $page != 'chatbot' && $page != 'history' && $page != 'map'): ?>
                    <!-- Tryb Edycji dla Admina -->
                    <form action="save_content.php" method="POST">
                        <input type="hidden" name="page" value="<?php echo htmlspecialchars($page); ?>">
                        <textarea name="content" id="editor">
                            <?php 
                            switch($page) {
                                case 'about': echo $cms['about_company']; break;
                                case 'contact': echo $cms['contact']; break;
                                case 'offer': echo $cms['offer']; break;
                                default: echo "Wybierz stronę do edycji."; break;
                            }
                            ?>
                        </textarea>
                        <button type="submit" class="btn btn-success mt-3">Zapisz zmiany</button>
                    </form>
                    <script>
                        ClassicEditor.create(document.querySelector('#editor')).catch(error => { console.error(error); });
                    </script>
                <?php else: ?>
                    <!-- Tryb Normalny / Specjalny -->
                    <?php
                    switch($page) {
                        case 'about':
                            echo "<h1>O firmie</h1>";
                            echo "<div>" . ($cms['about_company'] ?: "Brak treści.") . "</div>";
                            break;
                        case 'contact':
                            echo "<h1>Kontakt</h1>";
                            echo "<div>" . ($cms['contact'] ?: "Brak treści.") . "</div>";
                            break;
                        case 'map':
                            echo "<h1>Jak do nas dotrzeć</h1>";
                            if ($cms['google_map_link']) {
                                echo '<div class="ratio ratio-16x9">
                                        <iframe src="' . $cms['google_map_link'] . '" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                      </div>';
                            } else {
                                echo "Brak mapy.";
                            }
                            break;
                        case 'offer':
                            echo "<h1>Oferta</h1>";
                            echo "<div>" . ($cms['offer'] ?: "Brak treści.") . "</div>";
                            break;
                        case 'chatbot':
                            include 'pages/chatbot.php';
                            break;
                        case 'history':
                            include 'pages/history.php';
                            break;
                        default:
                            echo "<h1>Strona nie znaleziona</h1>";
                    }
                    ?>
                <?php endif; ?>
            </div>

            <?php if ($is_admin): ?>
            <div class="admin-footer">
                <h5>Ustawienia Logo</h5>
                <form action="upload_logo.php" method="POST" enctype="multipart/form-data">
                    <div class="input-group">
                        <input type="file" name="logo" class="form-control" accept=".svg,.png,.jpg">
                        <button class="btn btn-primary" type="submit">Wgraj nowe logo</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
