<?php
/**
 * Plik: index.php
 * Cel: Interfejs kursanta z poprawną strukturą AdSense i HTML5.
 */
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pracownik') {
    header("Location: pages/logowanie.php");
    exit();
}

$current_lesson = null;
if (isset($_GET['idl'])) {
    $idl = (int)$_GET['idl'];
    $stmt = $pdo->prepare("SELECT l.*, c.login as author FROM lekcje l JOIN coach c ON l.idc = c.idc WHERE l.idl = ?");
    $stmt->execute([$idl]);
    $current_lesson = $stmt->fetch();
    
    if ($current_lesson) {
        $stmt_log = $pdo->prepare("INSERT INTO logi_aktywnosci (rola, id_uzytkownika, akcja) VALUES ('pracownik', ?, ?)");
        $stmt_log->execute([$_SESSION['user_id'], "Przeglądanie lekcji: " . $current_lesson['nazwa']]);
    }
}
$lessons_list = $pdo->query("SELECT idl, nazwa FROM lekcje ORDER BY kolejnosc ASC, idl ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="System E-learningowy dla korporacji.">
    <title>Portal E-learningowy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Testowy skrypt Google AdSense -->
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1234567890123456" crossorigin="anonymous"></script>
    
    <style>
        body { overflow-x: hidden; }
        .sidebar { 
            min-height: 100vh; 
            background-color: #f8f9fa; 
            border-right: 1px solid #dee2e6; 
            padding: 20px; 
        }
        .main-content { padding: 30px; }
        .nav-link { color: #333; }
        .nav-link:hover { background-color: #e9ecef; }
        
        .adsense-placeholder { 
            border: 1px solid #ddd; 
            background: #fff;
            color: #777; 
            text-align: center; 
            min-height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 40px; 
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
        }

        .multimedia-box { 
            margin-top: 25px; 
            border-top: 1px solid #eee; 
            padding-top: 1rem; 
        }
        .multimedia-box img, .multimedia-box video { 
            max-width: 100%; 
            height: auto;
            border-radius: 8px; 
        }
        .lesson-body img { max-width: 100%; height: auto; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Lewy panel nawigacyjny -->
        <div class="col-lg-3 col-md-4 sidebar d-flex flex-column">
            <h4 class="mb-4">Menu Kursu</h4>
            
            <nav class="nav flex-column mb-auto">
                <?php foreach ($lessons_list as $item): ?>
                    <a class="nav-link mb-1 rounded <?php echo (isset($idl) && $idl == $item['idl']) ? 'bg-primary text-white' : ''; ?>" 
                       href="?idl=<?php echo $item['idl']; ?>">
                        <?php echo htmlspecialchars($item['nazwa']); ?>
                    </a>
                <?php endforeach; ?>
                
                <hr>
                <a class="nav-link fw-bold" href="index.php?page=podsumowanie">Historia wyników</a>
                <a class="nav-link fw-bold" href="index.php?page=testy">Wszystkie Testy</a>
            </nav>

            <div class="user-info mt-4 pt-3 border-top">
                <p class="small mb-1 text-muted">Zalogowany jako:</p>
                <p class="fw-bold mb-2"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <a href="database/logout_handler.php" class="btn btn-sm btn-outline-danger w-100">Wyloguj</a>
            </div>

            <div class="adsense-placeholder shadow-sm">
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="ca-pub-1234567890123456"
                     data-ad-slot="1111111111"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
            </div>
        </div>

        <!-- Prawy obszar roboczy -->
        <div class="col-lg-9 col-md-8 main-content">
            <?php 
            $page = $_GET['page'] ?? '';
            if ($current_lesson): ?>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                    <h2 class="mb-0"><?php echo htmlspecialchars($current_lesson['nazwa']); ?></h2>
                    <span class="badge bg-secondary">autor: <?php echo htmlspecialchars($current_lesson['author']); ?></span>
                </div>

                <article class="lesson-body mb-5">
                    <?php echo $current_lesson['tresc']; ?>
                </article>

                <?php if ($current_lesson['idt']): ?>
                    <div class="alert alert-warning shadow-sm d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Uwaga!</strong> Do tej lekcji przypisany jest test sprawdzający wiedzę.
                        </div>
                        <a href="test_view.php?idt=<?php echo $current_lesson['idt']; ?>" class="btn btn-warning fw-bold">Uruchom Test</a>
                    </div>
                <?php endif; ?>

                <?php if (!empty($current_lesson['plik_pdf'])): ?>
                    <section class="pdf-lesson-box mb-4">
                        <div class="ratio ratio-16x9 shadow-sm rounded overflow-hidden">
                            <embed src="pdf/lekcje/<?php echo $current_lesson['plik_pdf']; ?>" type="application/pdf" width="100%" height="600px" />
                        </div>
                    </section>
                <?php endif; ?>

                <?php if (!empty($current_lesson['plik_multimedialny'])): ?>
                    <section class="multimedia-box pt-4">
                        <h5 class="mb-3">Multimedia do lekcji:</h5>
                        <?php 
                        $file = $current_lesson['plik_multimedialny'];
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        $path = "lekcje/" . $file;
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                            <img src="<?php echo $path; ?>" class="shadow-sm img-fluid" alt="Grafika">
                        <?php elseif (in_array($ext, ['mp4', 'webm'])): ?>
                            <video controls class="shadow-sm w-100">
                                <source src="<?php echo $path; ?>" type="video/<?php echo $ext; ?>">
                            </video>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>

            <?php elseif ($page === 'testy'): ?>
                <h2 class="border-bottom pb-3 mb-4">Dostępne Testy</h2>
                <div class="list-group">
                    <?php
                    $tests = $pdo->query("SELECT * FROM test")->fetchAll();
                    if ($tests):
                        foreach ($tests as $t): ?>
                            <a href="test_view.php?idt=<?php echo $t['idt']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div><h5 class="mb-1"><?php echo htmlspecialchars($t['nazwa']); ?></h5><small class="text-muted">Czas: <?php echo $t['max_time']; ?> min</small></div>
                                <span class="btn btn-primary btn-sm">Start</span>
                            </a>
                        <?php endforeach;
                    else: echo "<p>Brak dostępnych testów.</p>";
                    endif; ?>
                </div>
            <?php elseif ($page === 'podsumowanie'): ?>
                <h2 class="border-bottom pb-3 mb-4">Historia wyników</h2>
                <table class="table table-striped shadow-sm">
                    <thead><tr><th>Test</th><th>Data</th><th>Wynik</th><th>Raport</th></tr></thead>
                    <tbody>
                        <?php
                        $results = $pdo->prepare("SELECT w.*, t.nazwa FROM wyniki w JOIN test t ON w.idt = t.idt WHERE w.idp = ? ORDER BY w.datetime DESC");
                        $results->execute([$_SESSION['user_id']]);
                        foreach ($results->fetchAll() as $r): ?>
                            <tr><td><?php echo htmlspecialchars($r['nazwa']); ?></td><td><?php echo $r['datetime']; ?></td><td><strong><?php echo $r['punkty']; ?> pkt</strong></td><td><a href="pdf/<?php echo $r['plik_pdf']; ?>" class="btn btn-sm btn-outline-success" target="_blank">Pobierz PDF</a></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/3429/3429153.png" style="width: 100px; opacity: 0.3;" alt="Ikona e-learning">
                    <h3 class="mt-4 text-muted">Wybierz lekcję, aby rozpocząć naukę</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Skrypt aktywujący AdSense (zgodnie z dokumentacją Google) -->
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</body>
</html>
