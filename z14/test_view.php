<?php
/**
 * Plik: test_view.php
 * Cel: Widok testu dla kursanta z odliczaniem czasu.
 */
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pracownik') {
    die("Brak uprawnień.");
}

$idt = isset($_GET['idt']) ? (int)$_GET['idt'] : 0;

// Pobierz dane testu
$stmt = $pdo->prepare("SELECT * FROM test WHERE idt = ?");
$stmt->execute([$idt]);
$test = $stmt->fetch();

if (!$test) die("Test nie istnieje.");

// Pobierz pytania
$stmt_q = $pdo->prepare("SELECT * FROM pytania WHERE idt = ?");
$stmt_q->execute([$idt]);
$questions = $stmt_q->fetchAll();

$max_time_sec = $test['max_time'] * 60; // Zakładamy, że w bazie są minuty
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Test: <?php echo htmlspecialchars($test['nazwa']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #timer { position: fixed; top: 20px; right: 20px; font-size: 1.5rem; z-index: 1000; }
        .question-card { margin-bottom: 20px; }
    </style>
</head>
<body class="bg-light p-4">

<div id="timer" class="badge bg-danger p-3 shadow">Czas: <span id="time_display">--:--</span></div>

<div class="container" style="max-width: 800px;">
    <h2 class="mb-4 text-center"><?php echo htmlspecialchars($test['nazwa']); ?></h2>
    
    <form id="test_form" action="test_submit.php" method="POST">
        <input type="hidden" name="idt" value="<?php echo $idt; ?>">
        
        <?php foreach ($questions as $index => $q): ?>
            <div class="card question-card shadow-sm">
                <div class="card-body">
                    <h5><?php echo ($index + 1) . ". " . htmlspecialchars($q['tresc_pytania']); ?></h5>
                    
                    <?php if (!empty($q['plik_multimedialny'])): ?>
                        <div class="text-center my-3">
                            <img src="../media/<?php echo $test['nazwa']; ?>/<?php echo $q['plik_multimedialny']; ?>" class="img-fluid rounded border" alt="Media do pytania">
                        </div>
                    <?php endif; ?>

                    <hr>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ans[<?php echo $q['idpyt']; ?>][]" value="a" id="q<?php echo $q['idpyt']; ?>a">
                        <label class="form-check-label" for="q<?php echo $q['idpyt']; ?>a">A) <?php echo htmlspecialchars($q['odpowiedz_a']); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ans[<?php echo $q['idpyt']; ?>][]" value="b" id="q<?php echo $q['idpyt']; ?>b">
                        <label class="form-check-label" for="q<?php echo $q['idpyt']; ?>b">B) <?php echo htmlspecialchars($q['odpowiedz_b']); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ans[<?php echo $q['idpyt']; ?>][]" value="c" id="q<?php echo $q['idpyt']; ?>c">
                        <label class="form-check-label" for="q<?php echo $q['idpyt']; ?>c">C) <?php echo htmlspecialchars($q['odpowiedz_c']); ?></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="ans[<?php echo $q['idpyt']; ?>][]" value="d" id="q<?php echo $q['idpyt']; ?>d">
                        <label class="form-check-label" for="q<?php echo $q['idpyt']; ?>d">D) <?php echo htmlspecialchars($q['odpowiedz_d']); ?></label>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 mb-5">KONIEC - WYŚLIJ TEST</button>
    </form>
</div>

<script>
    let timeLeft = <?php echo $max_time_sec; ?>;
    const display = document.querySelector('#time_display');
    const form = document.querySelector('#test_form');

    function startTimer() {
        const timerInterval = setInterval(() => {
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;

            display.textContent = `${minutes < 10 ? '0' : ''}${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

            if (--timeLeft < 0) {
                clearInterval(timerInterval);
                alert("Czas minął! Test zostanie automatycznie wysłany.");
                form.submit();
            }
        }, 1000);
    }

    startTimer();
</script>

</body>
</html>
