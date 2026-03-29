<?php
/**
 * Plik: pages/panel_coach.php
 * Cel: Zarządzanie lekcjami, testami i logami.
 */
session_start();
require_once '../database/database.php';

// Weryfikacja uprawnień (rola coach)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coach') {
    die("Brak uprawnień. Zaloguj się jako coach.");
}

// 1. Dodawanie Lekcji
if (isset($_POST['add_lesson'])) {
    $nazwa = $_POST['nazwa'];
    $tresc = $_POST['tresc'];
    $plik = "";
    if (!empty($_FILES['file']['name'])) {
        $target = "../lekcje/" . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $plik = basename($_FILES['file']['name']);
        }
    }
    $stmt = $pdo->prepare("INSERT INTO lekcje (idc, nazwa, tresc, plik_multimedialny) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $nazwa, $tresc, $plik]);
    echo "<div class='alert alert-success'>Lekcja dodana!</div>";
}

// 2. Dodawanie Testu
if (isset($_POST['add_test'])) {
    $test_name = $_POST['test_name'];
    $stmt = $pdo->prepare("INSERT INTO test (idc, nazwa, max_time) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $test_name, $_POST['max_time']]);
    
    // Tworzenie folderu na media testu (zgodnie z instrukcją)
    $test_dir = "../media/" . $test_name;
    if (!file_exists($test_dir)) {
        mkdir($test_dir, 0777, true);
    }
    
    echo "<div class='alert alert-success'>Test stworzony i folder przygotowany!</div>";
}

// 3. Dodawanie Pytań
if (isset($_POST['add_question'])) {
    $a = isset($_POST['correct_a']) ? 1 : 0;
    $b = isset($_POST['correct_b']) ? 1 : 0;
    $c = isset($_POST['correct_c']) ? 1 : 0;
    $d = isset($_POST['correct_d']) ? 1 : 0;
    
    $idt = $_POST['idt'];
    // Pobierz nazwę testu dla ścieżki pliku
    $stmt_t = $pdo->prepare("SELECT nazwa FROM test WHERE idt = ?");
    $stmt_t->execute([$idt]);
    $t_name = $stmt_t->fetchColumn();
    
    $plik = "";
    if (!empty($_FILES['q_file']['name'])) {
        $target = "../media/" . $t_name . "/" . basename($_FILES['q_file']['name']);
        if (move_uploaded_file($_FILES['q_file']['tmp_name'], $target)) {
            $plik = basename($_FILES['q_file']['name']);
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO pytania (idt, tresc_pytania, odpowiedz_a, odpowiedz_b, odpowiedz_c, odpowiedz_d, a, b, c, d, plik_multimedialny) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$idt, $_POST['q_text'], $_POST['ans_a'], $_POST['ans_b'], $_POST['ans_c'], $_POST['ans_d'], $a, $b, $c, $d, $plik]);
    echo "<div class='alert alert-success'>Pytanie dodane!</div>";
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Coacha - E-learning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Witaj, <?php echo $_SESSION['username']; ?> (Coach)</span>
        <a href="../database/logout_handler.php" class="btn btn-outline-danger btn-sm">Wyloguj</a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row">
        <!-- Zarządzanie Lekcjami -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Tworzenie Lekcji (CKEditor)</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Tytuł Lekcji</label>
                            <input type="text" name="nazwa" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="tresc" id="lesson_editor"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Plik (Grafika/MP3/MP4)</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                        <button type="submit" name="add_lesson" class="btn btn-primary w-100">Opublikuj Lekcję</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tworzenie Testów -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">Nowy Test</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="text" name="test_name" class="form-control mb-2" placeholder="Nazwa testu" required>
                        <input type="number" name="max_time" class="form-control mb-2" placeholder="Czas (minuty)" required>
                        <button type="submit" name="add_test" class="btn btn-success w-100">Utwórz Test</button>
                    </form>
                </div>
            </div>

            <!-- Dodawanie Pytań -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">Dodaj Pytanie do Testu</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <select name="idt" class="form-select mb-2" required>
                            <?php
                            $tests = $pdo->query("SELECT idt, nazwa FROM test")->fetchAll();
                            foreach ($tests as $t) echo "<option value='{$t['idt']}'>{$t['nazwa']}</option>";
                            ?>
                        </select>
                        <textarea name="q_text" class="form-control mb-2" placeholder="Treść pytania" required></textarea>
                        <div class="mb-2">
                            <label class="small text-muted">Plik multimedialny pytania</label>
                            <input type="file" name="q_file" class="form-control form-control-sm">
                        </div>
                        <div class="input-group mb-1"><span class="input-group-text small">A</span><input type="text" name="ans_a" class="form-control form-control-sm"><div class="input-group-text"><input type="checkbox" name="correct_a"></div></div>
                        <div class="input-group mb-1"><span class="input-group-text small">B</span><input type="text" name="ans_b" class="form-control form-control-sm"><div class="input-group-text"><input type="checkbox" name="correct_b"></div></div>
                        <div class="input-group mb-1"><span class="input-group-text small">C</span><input type="text" name="ans_c" class="form-control form-control-sm"><div class="input-group-text"><input type="checkbox" name="correct_c"></div></div>
                        <div class="input-group mb-1"><span class="input-group-text small">D</span><input type="text" name="ans_d" class="form-control form-control-sm"><div class="input-group-text"><input type="checkbox" name="correct_d"></div></div>
                        <button type="submit" name="add_question" class="btn btn-info w-100 mt-2 btn-sm">Dodaj Pytanie</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Wyniki Pracowników -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">Wyniki testów pracowników</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>ID</th><th>Pracownik</th><th>Test</th><th>Data</th><th>Punkty</th><th>Raport</th></tr></thead>
                <tbody>
                    <?php
                    $results = $pdo->query("SELECT w.*, p.login as worker, t.nazwa as test_name 
                                           FROM wyniki w 
                                           JOIN pracownik p ON w.idp = p.idp 
                                           JOIN test t ON w.idt = t.idt 
                                           ORDER BY w.datetime DESC")->fetchAll();
                    foreach ($results as $r) {
                        echo "<tr><td>{$r['idw']}</td><td>{$r['worker']}</td><td>{$r['test_name']}</td><td>{$r['datetime']}</td><td><strong>{$r['punkty']}</strong></td>
                              <td><a href='../pdf/{$r['plik_pdf']}' class='btn btn-sm btn-outline-success' target='_blank'>PDF</a></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Logi Aktywności -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Logi aktywności pracowników</div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>ID</th><th>Rola</th><th>User ID</th><th>Akcja</th><th>Data</th></tr></thead>
                <tbody>
                    <?php
                    $logs = $pdo->query("SELECT * FROM logi_aktywnosci ORDER BY datetime DESC LIMIT 20")->fetchAll();
                    foreach ($logs as $l) {
                        echo "<tr><td>{$l['id_logu']}</td><td>{$l['rola']}</td><td>{$l['id_uzytkownika']}</td><td>{$l['akcja']}</td><td>{$l['datetime']}</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>CKEDITOR.replace('lesson_editor');</script>
</body>
</html>
