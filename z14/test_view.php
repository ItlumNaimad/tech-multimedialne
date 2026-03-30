<?php
/**
 * Plik: test_view.php
 * Cel: Widok testu z dynamicznymi odpowiedziami i obsługą typów wyboru.
 */
session_start();
require_once 'database/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pracownik') {
    die("Brak uprawnień.");
}

$idt = isset($_GET['idt']) ? (int)$_GET['idt'] : 0;

$stmt = $pdo->prepare("SELECT * FROM test WHERE idt = ?");
$stmt->execute([$idt]);
$test = $stmt->fetch();

if (!$test) die("Test nie istnieje.");

// Pobierz pytania
$stmt_q = $pdo->prepare("SELECT * FROM pytania WHERE idt = ?");
$stmt_q->execute([$idt]);
$questions = $stmt_q->fetchAll();

$max_time_sec = $test['max_time'] * 60;
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
        .choice-info { font-size: 0.85rem; color: #6c757d; font-style: italic; }
    </style>
</head>
<body class="bg-light p-4">

<div id="timer" class="badge bg-danger p-3 shadow">Pozostało: <span id="time_display">--:--</span></div>

<div class="container" style="max-width: 800px;">
    <h2 class="mb-4 text-center"><?php echo htmlspecialchars($test['nazwa']); ?></h2>
    <p class="text-center text-muted">Próg zaliczenia: <?php echo $test['prog_zaliczenia']; ?>%</p>
    
    <form id="test_form" action="test_submit.php" method="POST">
        <input type="hidden" name="idt" value="<?php echo $idt; ?>">
        
        <?php foreach ($questions as $index => $q): ?>
            <div class="card question-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h5><?php echo ($index + 1) . ". " . htmlspecialchars($q['tresc_pytania']); ?></h5>
                        <span class="choice-info">
                            <?php echo ($q['typ_wyboru'] === 'multi') ? '(Wielokrotny wybór)' : '(Jednokrotny wybór)'; ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($q['plik_multimedialny'])): ?>
                        <div class="text-center my-3">
                            <!-- Poprawiona ścieżka do mediów -->
                            <img src="media/<?php echo $test['nazwa']; ?>/<?php echo $q['plik_multimedialny']; ?>" class="img-fluid rounded border shadow-sm" alt="Media do pytania">
                        </div>
                    <?php endif; ?>

                    <hr>
                    <?php
                    // Pobierz odpowiedzi dla tego pytania
                    $stmt_ans = $pdo->prepare("SELECT * FROM odpowiedzi WHERE idpyt = ?");
                    $stmt_ans->execute([$q['idpyt']]);
                    $answers = $stmt_ans->fetchAll();
                    
                    $input_type = ($q['typ_wyboru'] === 'multi') ? 'checkbox' : 'radio';
                    
                    foreach ($answers as $ans): ?>
                        <div class="form-check py-1">
                            <input class="form-check-input" type="<?php echo $input_type; ?>" 
                                   name="ans[<?php echo $q['idpyt']; ?>][]" 
                                   value="<?php echo $ans['idodp']; ?>" 
                                   id="ans<?php echo $ans['idodp']; ?>">
                            <label class="form-check-label" for="ans<?php echo $ans['idodp']; ?>">
                                <?php echo htmlspecialchars($ans['tresc']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3 mb-5 shadow">WYŚLIJ TEST I ZAKOŃCZ</button>
    </form>
</div>

<script>
    let timeLeft = <?php echo $max_time_sec; ?>;
    const display = document.querySelector('#time_display');
    const form = document.querySelector('#test_form');

    function startTimer() {
        if (timeLeft <= 0) {
            display.textContent = "Bez limitu";
            return;
        }
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
