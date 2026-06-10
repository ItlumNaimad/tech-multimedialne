<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = getCurrentUserId();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $q1 = $_POST['q1'] ?? '';
    $q2 = $_POST['q2'] ?? '';
    
    $score = 0;
    if ($q1 === 'b') $score += 50;
    if ($q2 === 'c') $score += 50;
    
    $passed = $score === 100 ? 1 : 0;
    
    $stmt = $pdo->prepare("INSERT INTO elearning_results (user_id, score, passed) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $score, $passed]);
    
    if ($passed) {
        $message = '<div class="alert alert-success">Gratulacje! Zdałeś test na 100% i jesteś certyfikowanym Zbijaczem Bąków.</div>';
    } else {
        $message = '<div class="alert alert-danger">Niestety, uzyskałeś '.$score.'%. Spróbuj ponownie uważej czytając lekcję.</div>';
    }
}

// Historia wyników usera
$stmt = $pdo->prepare("SELECT * FROM elearning_results WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();

require_once 'header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h3 class="card-title text-success"><i class="bi bi-mortarboard"></i> e-Learning: Kurs podstawowy</h3>
                <h5 class="mt-4">Lekcja 1: Podstawy patrzenia w sufit</h5>
                <p>
                    Profesjonalne zbijanie bąków wymaga odpowiedniego przygotowania fizycznego i mentalnego.
                    Kluczową zasadą jest znalezienie najwygodniejszej pozycji horyzontalnej. 
                    Według norm ISO z 2026 roku, optymalny czas ciągłego wpatrywania się w sufit bez mrugania to 45 sekund.
                </p>
                
                <hr class="my-4">
                
                <h5>Test wiedzy (Sprawdź się)</h5>
                <?= $message ?>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">1. Jaka jest najważniejsza zasada profesjonalnego zbijania bąków?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q1" value="a" required> Aktywność fizyczna
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q1" value="b"> Znalezienie pozycji horyzontalnej
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q1" value="c"> Oglądanie Netflixa
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">2. Jaki jest optymalny czas wpatrywania się w sufit bez mrugania wg norm ISO?</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q2" value="a" required> 15 sekund
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q2" value="b"> 30 minut
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="q2" value="c"> 45 sekund
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success"><i class="bi bi-journal-check"></i> Zakończ Test</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">Twoje wyniki (System monitorowania)</h5>
            </div>
            <ul class="list-group list-group-flush">
                <?php if(empty($results)): ?>
                    <li class="list-group-item text-muted">Brak podejść do testów.</li>
                <?php else: ?>
                    <?php foreach($results as $r): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></span>
                            <?php if($r['passed']): ?>
                                <span class="badge bg-success rounded-pill"><?= $r['score'] ?>% ZDANY</span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-pill"><?= $r['score'] ?>% OBLANY</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
