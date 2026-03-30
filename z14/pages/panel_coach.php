<?php
/**
 * Plik: pages/panel_coach.php
 * Cel: Zarządzanie lekcjami, testami i logami z nowymi funkcjonalnościami.
 */
session_start();
require_once '../database/database.php';

// Weryfikacja uprawnień (rola coach)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'coach') {
    die("Brak uprawnień. Zaloguj się jako coach.");
}

// 1. Dodawanie/Edycja Lekcji
if (isset($_POST['add_lesson'])) {
    $nazwa = $_POST['nazwa'];
    $tresc = $_POST['tresc'] ?? '';
    $kolejnosc = (int)($_POST['kolejnosc'] ?? 0);
    $idt = !empty($_POST['idt']) ? (int)$_POST['idt'] : null;
    
    // Obsługa pliku multimedialnego
    $plik = "";
    if (!empty($_FILES['file']['name'])) {
        $target = "../lekcje/" . basename($_FILES['file']['name']);
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $plik = basename($_FILES['file']['name']);
        }
    }
    
    // Obsługa pliku PDF
    $plik_pdf = "";
    if (!empty($_FILES['file_pdf']['name'])) {
        if (!file_exists("../pdf/lekcje/")) {
            mkdir("../pdf/lekcje/", 0777, true);
        }
        $target_pdf = "../pdf/lekcje/" . basename($_FILES['file_pdf']['name']);
        if (move_uploaded_file($_FILES['file_pdf']['tmp_name'], $target_pdf)) {
            $plik_pdf = basename($_FILES['file_pdf']['name']);
        }
    }

    $stmt = $pdo->prepare("INSERT INTO lekcje (idc, idt, nazwa, tresc, plik_multimedialny, plik_pdf, kolejnosc) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $idt, $nazwa, $tresc, $plik, $plik_pdf, $kolejnosc]);
    echo "<div class='alert alert-success'>Lekcja dodana pomyślnie!</div>";
}

// 2. Dodawanie Testu
if (isset($_POST['add_test'])) {
    $test_name = $_POST['test_name'];
    $max_time = (int)$_POST['max_time'];
    $prog = (int)$_POST['prog_zaliczenia'];
    
    $stmt = $pdo->prepare("INSERT INTO test (idc, nazwa, max_time, prog_zaliczenia) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $test_name, $max_time, $prog]);
    
    $test_dir = "../media/" . $test_name;
    if (!file_exists($test_dir)) {
        mkdir($test_dir, 0777, true);
    }
    
    echo "<div class='alert alert-success'>Test stworzony!</div>";
}

// 3. Dodawanie Pytań (z dynamicznymi odpowiedziami)
if (isset($_POST['add_question'])) {
    $idt = $_POST['idt'];
    $q_text = $_POST['q_text'];
    $typ = $_POST['typ_wyboru']; // single lub multi
    
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
    
    $stmt = $pdo->prepare("INSERT INTO pytania (idt, tresc_pytania, typ_wyboru, plik_multimedialny) VALUES (?, ?, ?, ?)");
    $stmt->execute([$idt, $q_text, $typ, $plik]);
    $idpyt = $pdo->lastInsertId();
    
    // Dodawanie odpowiedzi
    if (isset($_POST['answers']) && is_array($_POST['answers'])) {
        foreach ($_POST['answers'] as $idx => $ans_text) {
            if (empty($ans_text)) continue;
            $is_correct = isset($_POST['correct'][$idx]) ? 1 : 0;
            $stmt_ans = $pdo->prepare("INSERT INTO odpowiedzi (idpyt, tresc, czy_poprawna) VALUES (?, ?, ?)");
            $stmt_ans->execute([$idpyt, $ans_text, $is_correct]);
        }
    }
    
    echo "<div class='alert alert-success'>Pytanie dodane!</div>";
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Coacha (Rozszerzony) - E-learning</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <style>
        .test-bar { background: #e9ecef; border-radius: 5px; padding: 15px; margin-bottom: 20px; border-left: 5px solid #28a745; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">Witaj, <?php echo $_SESSION['username']; ?> (Coach)</span>
        <a href="../database/logout_handler.php" class="btn btn-outline-danger btn-sm">Wyloguj</a>
    </div>
</nav>

<div class="container pb-5">
    
    <!-- Pasek z utworzonymi testami i ilością lekcji -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Statystyki Testów i Lekcji</div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        $test_stats = $pdo->query("SELECT t.idt, t.nazwa, t.prog_zaliczenia, t.max_time, (SELECT COUNT(*) FROM lekcje l WHERE l.idt = t.idt) as lesson_count FROM test t")->fetchAll();
                        foreach ($test_stats as $ts): ?>
                            <div class="col-md-4">
                                <div class="test-bar shadow-sm">
                                    <h5 class="mb-1 text-success"><?php echo htmlspecialchars($ts['nazwa']); ?></h5>
                                    <p class="mb-0 small text-muted">Przypisane lekcje: <strong><?php echo $ts['lesson_count']; ?></strong></p>
                                    <p class="mb-0 small text-muted">Próg: <strong><?php echo $ts['prog_zaliczenia']; ?>%</strong> | Czas: <strong><?php echo $ts['max_time']; ?> min</strong></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Zarządzanie Lekcjami -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Tworzenie Lekcji (Zarządzanie Kolejnością)</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Tytuł Lekcji</label>
                            <input type="text" name="nazwa" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kolejność (Liczba)</label>
                                <input type="number" name="kolejnosc" class="form-control" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Przypisz do Testu</label>
                                <select name="idt" class="form-select">
                                    <option value="">-- Brak testu --</option>
                                    <?php foreach ($test_stats as $ts) echo "<option value='{$ts['idt']}'>{$ts['nazwa']}</option>"; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Treść Lekcji</label>
                            <textarea name="tresc" id="lesson_editor"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Grafika/Multimedia</label>
                                <input type="file" name="file" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Plik PDF</label>
                                <input type="file" name="file_pdf" class="form-control" accept=".pdf">
                            </div>
                        </div>
                        <button type="submit" name="add_lesson" class="btn btn-primary w-100">Opublikuj Lekcję</button>
                    </form>
                </div>
            </div>

            <!-- Lista lekcji (podgląd kolejności) -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">Aktualna kolejność lekcji</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php
                        $all_lessons = $pdo->query("SELECT * FROM lekcje ORDER BY kolejnosc ASC")->fetchAll();
                        foreach ($all_lessons as $l): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>[<?php echo $l['kolejnosc']; ?>] <?php echo htmlspecialchars($l['nazwa']); ?></span>
                                <small class="text-muted"><?php echo $l['idt'] ? 'Test przypisany' : 'Brak testu'; ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tworzenie Testów i Pytań -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">Nowy Test (Próg i Czas)</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="text" name="test_name" class="form-control mb-2" placeholder="Nazwa testu" required>
                        <div class="row g-2">
                            <div class="col-6"><input type="number" name="max_time" class="form-control mb-2" placeholder="Czas (min)" required></div>
                            <div class="col-6"><input type="number" name="prog_zaliczenia" class="form-control mb-2" placeholder="Próg %" required min="0" max="100"></div>
                        </div>
                        <button type="submit" name="add_test" class="btn btn-success w-100">Utwórz Test</button>
                    </form>
                </div>
            </div>

            <!-- Dodawanie Pytań z dynamicznymi odpowiedziami -->
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">Dodaj Pytanie (Wielokrotny Wybór)</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <select name="idt" class="form-select mb-2" required>
                            <?php foreach ($test_stats as $ts) echo "<option value='{$ts['idt']}'>{$ts['nazwa']}</option>"; ?>
                        </select>
                        <textarea name="q_text" class="form-control mb-2" placeholder="Treść pytania" required></textarea>
                        
                        <div class="mb-3">
                            <label class="small fw-bold">Typ wyboru:</label>
                            <select name="typ_wyboru" class="form-select form-select-sm">
                                <option value="single">Jednokrotny wybór</option>
                                <option value="multi">Wielokrotny wybór</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="small text-muted">Media pytania</label>
                            <input type="file" name="q_file" class="form-control form-control-sm">
                        </div>

                        <div id="answers_container">
                            <label class="small fw-bold">Odpowiedzi (zaznacz poprawne):</label>
                            <div class="input-group mb-1">
                                <input type="text" name="answers[]" class="form-control form-control-sm" placeholder="Odpowiedź 1">
                                <div class="input-group-text"><input type="checkbox" name="correct[0]"></div>
                            </div>
                            <div class="input-group mb-1">
                                <input type="text" name="answers[]" class="form-control form-control-sm" placeholder="Odpowiedź 2">
                                <div class="input-group-text"><input type="checkbox" name="correct[1]"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mb-3 w-100" onclick="addAnswer()">+ Dodaj kolejną odpowiedź</button>

                        <button type="submit" name="add_question" class="btn btn-info w-100 btn-sm">Zapisz Pytanie</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    ClassicEditor.create( document.querySelector( '#lesson_editor' ) ).catch( error => { console.error( error ); } );

    let answerCount = 2;
    function addAnswer() {
        const container = document.getElementById('answers_container');
        const div = document.createElement('div');
        div.className = 'input-group mb-1';
        div.innerHTML = `
            <input type="text" name="answers[]" class="form-control form-control-sm" placeholder="Odpowiedź ${answerCount + 1}">
            <div class="input-group-text"><input type="checkbox" name="correct[${answerCount}]"></div>
        `;
        container.appendChild(div);
        answerCount++;
    }
</script>
</body>
</html>
