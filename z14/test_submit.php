<?php
/**
 * Plik: test_submit.php
 * Cel: Sprawdzenie testu, generowanie PDF (V4 - Uprawnienia i Ścieżki).
 */
session_start();
require_once 'database/database.php';

// Próba nadania uprawnień do plików czcionek (częsty problem na hostingach)
@chmod(__DIR__ . '/font/unifont/DejaVuSansCondensed.ttf', 0644);
@chmod(__DIR__ . '/font/unifont/DejaVuSansCondensed-Bold.ttf', 0644);
@chmod(__DIR__ . '/font/unifont/', 0755);

// Definiujemy ścieżkę do czcionek tFPDF - używamy relatywnej dla większej przenośności
if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', 'font/');
}

require_once 'tfpdf.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pracownik') {
    die("Brak uprawnień.");
}

$idt = (int)$_POST['idt'];
$idp = $_SESSION['user_id'];
$answers_post = $_POST['ans'] ?? [];

$stmt_t = $pdo->prepare("SELECT * FROM test WHERE idt = ?");
$stmt_t->execute([$idt]);
$test = $stmt_t->fetch();
if (!$test) die("Test nie istnieje.");

$stmt_q = $pdo->prepare("SELECT * FROM pytania WHERE idt = ?");
$stmt_q->execute([$idt]);
$questions = $stmt_q->fetchAll();

$total_points = 0;
$report_data = [];

foreach ($questions as $q) {
    $q_id = $q['idpyt'];
    $user_choices = $answers_post[$q_id] ?? [];
    $stmt_ans = $pdo->prepare("SELECT * FROM odpowiedzi WHERE idpyt = ?");
    $stmt_ans->execute([$q_id]);
    $all_answers = $stmt_ans->fetchAll();
    
    $correct_ids = [];
    foreach ($all_answers as $ans) {
        if ($ans['czy_poprawna']) $correct_ids[] = (string)$ans['idodp'];
    }
    
    sort($user_choices);
    sort($correct_ids);
    $is_correct = (!empty($correct_ids) && $user_choices === $correct_ids);
    if ($is_correct) $total_points++;
    
    $report_data[] = [
        'q' => $q['tresc_pytania'],
        'user' => $user_choices,
        'all_options' => $all_answers
    ];
}

$percent = count($questions) > 0 ? ($total_points / count($questions)) * 100 : 0;
$passed = ($percent >= $test['prog_zaliczenia']);

$stmt_log = $pdo->prepare("INSERT INTO logi_aktywnosci (rola, id_uzytkownika, akcja) VALUES ('pracownik', ?, ?)");
$stmt_log->execute([$idp, "Ukończono test: " . $test['nazwa'] . ", wynik: $total_points"]);

// GENEROWANIE PDF
$pdf_filename = "test_res_" . $idp . "_" . $idt . "_" . time() . ".pdf";
$pdf_path = "pdf/" . $pdf_filename;

if (!file_exists('pdf')) {
    mkdir('pdf', 0777, true);
}

try {
    $pdf = new tFPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    
    // Sprawdzamy dostępność pliku przez PHP przed tFPDF
    $check_path = __DIR__ . '/font/unifont/DejaVuSansCondensed.ttf';
    if (!is_readable($check_path)) {
        throw new Exception("Błąd: Plik czcionki istnieje, ale PHP nie może go odczytać (uprawnienia). Ścieżka: $check_path");
    }

    $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
    $pdf->AddFont('DejaVu', 'B', 'DejaVuSansCondensed-Bold.ttf', true);
    
    $pdf->SetFont('DejaVu', 'B', 16);
    $pdf->Cell(0, 10, "RAPORT Z TESTU: " . $test['nazwa'], 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('DejaVu', '', 11);
    $pdf->Cell(0, 7, "Użytkownik: " . $_SESSION['username'], 0, 1);
    $pdf->Cell(0, 7, "Data: " . date("Y-m-d H:i:s"), 0, 1);
    $pdf->Cell(0, 7, "Wynik: $total_points / " . count($questions) . " (" . round($percent) . "%)", 0, 1);
    
    $pdf->Ln(5);
    if ($passed) {
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(0, 10, "STATUS: ZALICZONY", 0, 1);
    } else {
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(0, 10, "STATUS: NIEZALICZONY", 0, 1);
    }
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(5);

    foreach ($report_data as $idx => $data) {
        $pdf->SetFont('DejaVu', 'B', 11);
        $pdf->MultiCell(0, 7, ($idx+1) . ". " . $data['q'], 0, 'L');
        
        $pdf->SetFont('DejaVu', '', 10);
        foreach ($data['all_options'] as $ans) {
            $is_user_selected = in_array((string)$ans['idodp'], $data['user']);
            $is_correct_ans = $ans['czy_poprawna'];
            
            if ($is_user_selected && $is_correct_ans) {
                $pdf->SetTextColor(0, 128, 0);
                $box = "[X] ";
                $tag = " (Prawidłowa)";
            } elseif ($is_user_selected && !$is_correct_ans) {
                $pdf->SetTextColor(255, 0, 0);
                $box = "[X] ";
                $tag = " (Błędna)";
            } elseif (!$is_user_selected && $is_correct_ans) {
                $pdf->SetTextColor(255, 0, 0);
                $box = "[ ] ";
                $tag = " (Prawidłowa - POMINIĘTO)";
            } else {
                $pdf->SetTextColor(100, 100, 100);
                $box = "[ ] ";
                $tag = "";
            }

            $pdf->Cell(10);
            $pdf->MultiCell(0, 6, $box . $ans['tresc'] . $tag, 0, 'L');
            $pdf->SetTextColor(0, 0, 0);
        }
        $pdf->Ln(3);
    }

    $pdf->Output('F', $pdf_path);
} catch (Exception $e) {
    error_log("Błąd PDF: " . $e->getMessage());
    $pdf_filename = "";
    $pdf_error = $e->getMessage();
}

$stmt_res = $pdo->prepare("INSERT INTO wyniki (idp, idt, punkty, plik_pdf) VALUES (?, ?, ?, ?)");
$stmt_res->execute([$idp, $idt, $total_points, $pdf_filename]);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wynik Testu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container text-center">
        <div class="card shadow p-5" style="max-width: 600px; margin: auto;">
            <h2 class="<?php echo $passed ? 'text-success' : 'text-danger'; ?> mb-4">
                <?php echo $passed ? 'ZALICZONE!' : 'NIEZALICZONE'; ?>
            </h2>
            <p class="fs-4">Twój wynik: <strong><?php echo $total_points; ?> / <?php echo count($questions); ?></strong> (<?php echo round($percent); ?>%)</p>
            <hr>
            <div class="mt-4">
                <?php if ($pdf_filename): ?>
                    <a href="pdf/<?php echo $pdf_filename; ?>" class="btn btn-success btn-lg w-100 mb-2 shadow" target="_blank">Pobierz Raport PDF</a>
                <?php else: ?>
                    <div class="alert alert-warning small">
                        Raport PDF nie został wygenerowany.<br>
                        <strong>Szczegóły:</strong> <?php echo isset($pdf_error) ? htmlspecialchars($pdf_error) : 'Nieznany błąd systemu plików.'; ?>
                    </div>
                <?php endif; ?>
                <a href="index.php" class="btn btn-outline-secondary w-100">Wróć do strony głównej</a>
            </div>
        </div>
    </div>
</body>
</html>
