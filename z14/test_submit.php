<?php
/**
 * Plik: test_submit.php
 * Cel: Sprawdzenie testu, generowanie PDF z polskimi znakami (CP1250).
 */
session_start();
require_once 'database/database.php';

// Weryfikacja obecności oficjalnej biblioteki FPDF
if (file_exists('fpdf.php')) {
    require 'fpdf.php';
    $has_fpdf = true;
} else {
    $has_fpdf = false;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pracownik') {
    die("Brak uprawnień.");
}

$idt = (int)$_POST['idt'];
$idp = $_SESSION['user_id'];
$answers_post = $_POST['ans'] ?? [];

// Pobierz dane testu
$stmt_t = $pdo->prepare("SELECT * FROM test WHERE idt = ?");
$stmt_t->execute([$idt]);
$test = $stmt_t->fetch();

if (!$test) die("Test nie istnieje.");

// Pobierz pytania
$stmt_q = $pdo->prepare("SELECT * FROM pytania WHERE idt = ?");
$stmt_q->execute([$idt]);
$questions = $stmt_q->fetchAll();

$total_points = 0;
$report_data = [];

foreach ($questions as $q) {
    $q_id = $q['idpyt'];
    $user_choices = $answers_post[$q_id] ?? [];
    
    // Pobierz wszystkie możliwe odpowiedzi dla tego pytania
    $stmt_ans = $pdo->prepare("SELECT * FROM odpowiedzi WHERE idpyt = ?");
    $stmt_ans->execute([$q_id]);
    $all_answers = $stmt_ans->fetchAll();
    
    $correct_ids = [];
    foreach ($all_answers as $ans) {
        if ($ans['czy_poprawna']) $correct_ids[] = (string)$ans['idodp'];
    }
    
    // Sprawdzenie czy wybór użytkownika pokrywa się z poprawnymi odpowiedziami
    sort($user_choices);
    sort($correct_ids);
    $is_correct = (!empty($correct_ids) && $user_choices === $correct_ids);
    
    if ($is_correct) $total_points++;
    
    $report_data[] = [
        'q' => $q['tresc_pytania'],
        'status' => $is_correct,
        'user' => $user_choices,
        'all_options' => $all_answers
    ];
}

$percent = count($questions) > 0 ? ($total_points / count($questions)) * 100 : 0;
$passed = ($percent >= $test['prog_zaliczenia']);

// 1. Logowanie
$stmt_log = $pdo->prepare("INSERT INTO logi_aktywnosci (rola, id_uzytkownika, akcja) VALUES ('pracownik', ?, ?)");
$stmt_log->execute([$idp, "Ukonczono test: " . $test['nazwa'] . ", wynik: $total_points"]);

// 2. Generowanie PDF (Oficjalny FPDF z polskimi czcionkami)
$pdf_filename = "";
if ($has_fpdf) {
    try {
        $pdf = new FPDF('P', 'mm', 'A4');
        
        // REJESTRUJEMY POLSKIE CZCIONKI (z tablicą CP1250)
        $pdf->AddFont('ArialPL', '', 'arialpl.php');
        $pdf->AddFont('ArialPL', 'B', 'arialplb.php');
        
        $pdf->AddPage();
        
        // Tytuł
        $pdf->SetFont('ArialPL', 'B', 16);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1250', "RAPORT Z TESTU: " . $test['nazwa']), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('ArialPL', '', 11);
        $pdf->Cell(0, 7, iconv('UTF-8', 'windows-1250', "Użytkownik: " . $_SESSION['username']), 0, 1);
        $pdf->Cell(0, 7, iconv('UTF-8', 'windows-1250', "Data wykonania: " . date("Y-m-d H:i:s")), 0, 1);
        $pdf->Cell(0, 7, iconv('UTF-8', 'windows-1250', "Wynik punktowy: $total_points / " . count($questions)), 0, 1);
        $pdf->Cell(0, 7, iconv('UTF-8', 'windows-1250', "Procentowo: " . round($percent) . "%"), 0, 1);
        
        if ($passed) {
            $pdf->SetTextColor(0, 150, 0);
            $pdf->SetFont('ArialPL', 'B', 12);
            $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1250', "STATUS: ZALICZONY"), 0, 1);
        } else {
            $pdf->SetTextColor(200, 0, 0);
            $pdf->SetFont('ArialPL', 'B', 12);
            $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1250', "STATUS: NIEZALICZONY"), 0, 1);
        }
        $pdf->Ln(10);

        // Szczegóły pytań
        foreach ($report_data as $idx => $data) {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('ArialPL', 'B', 11);
            $pdf->MultiCell(0, 7, iconv('UTF-8', 'windows-1250', ($idx+1) . ". " . $data['q']), 0, 'L');
            
            $pdf->SetFont('ArialPL', '', 10);
            foreach ($data['all_options'] as $ans) {
                $is_user_selected = in_array((string)$ans['idodp'], $data['user']);
                $is_correct_ans = $ans['czy_poprawna'];
                
                $label = "";
                if ($is_correct_ans) {
                    $pdf->SetTextColor(0, 128, 0);
                    $label = iconv('UTF-8', 'windows-1250', " (POPRAWNA ODPOWIEDŹ)");
                } elseif ($is_user_selected) {
                    $pdf->SetTextColor(200, 0, 0);
                    $label = iconv('UTF-8', 'windows-1250', " (ZŁA ODPOWIEDŹ)");
                } else {
                    $pdf->SetTextColor(100, 100, 100);
                    $label = "";
                }

                $box = $is_user_selected ? "[X] " : "[ ] ";
                $pdf->Cell(10);
                $pdf->MultiCell(0, 6, iconv('UTF-8', 'windows-1250', $box . $ans['tresc']) . $label, 0, 'L');
            }
            $pdf->Ln(4);
        }

        $pdf_filename = "test_res_" . $idp . "_" . $idt . "_" . time() . ".pdf";
        $pdf_path = "pdf/" . $pdf_filename;
        $pdf->Output('F', $pdf_path);
        
    } catch (Exception $e) {
        $pdf_filename = "";
    }
}

// 3. Zapis wyniku do bazy
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
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
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
                    <div class="alert alert-warning small">Raport PDF nie został wygenerowany.</div>
                <?php endif; ?>
                <a href="index.php" class="btn btn-outline-secondary w-100">Wróć do strony głównej</a>
            </div>
        </div>
    </div>
</body>
</html>
