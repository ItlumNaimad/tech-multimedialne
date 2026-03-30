<?php
/**
 * Plik: test_submit.php
 * Cel: Sprawdzenie testu, kolorowy raport PDF z polskimi znakami.
 */
session_start();
require_once 'database/database.php';

// Sprawdź czy biblioteka FPDF istnieje
$has_fpdf = false;
if (file_exists('fpdf.php')) {
    require 'fpdf.php';
    $has_fpdf = true;
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

// Pobierz pytania
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

// 2. Generowanie PDF
$pdf_filename = "";
if ($has_fpdf) {
    try {
        $pdf = new FPDF();
        $pdf->AddPage();
        
        // Nagłówek
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1250', "RAPORT Z TESTU: " . $test['nazwa']), 0, 1);
        $pdf->Cell(0, 7, iconv('UTF-8', 'windows-1250', "Uzytkownik: " . $_SESSION['username']), 0, 1);
        $pdf->Cell(0, 7, iconv('UTF-8', 'windows-1250', "Wynik: $total_points / " . count($questions) . " (" . round($percent) . "%)"), 0, 1);
        
        if ($passed) {
            $pdf->SetTextColor(0, 150, 0);
            $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1250', "STATUS: ZALICZONY"), 0, 1);
        } else {
            $pdf->SetTextColor(200, 0, 0);
            $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1250', "STATUS: NIEZALICZONY"), 0, 1);
        }
        $pdf->Ln(10);

        foreach ($report_data as $idx => $data) {
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(0, 7, iconv('UTF-8', 'windows-1250', ($idx+1) . ". " . $data['q']));
            
            foreach ($data['all_options'] as $ans) {
                $is_user_selected = in_array((string)$ans['idodp'], $data['user']);
                $is_correct = $ans['czy_poprawna'];
                
                // Wybór koloru
                if ($is_correct) {
                    $pdf->SetTextColor(0, 128, 0); // Zielony dla poprawnych odpowiedzi
                    $label = " (POPRAWNA)";
                } else {
                    $pdf->SetTextColor(0, 0, 0);
                    $label = "";
                }
                
                if ($is_user_selected && !$is_correct) {
                    $pdf->SetTextColor(255, 0, 0); // Czerwony, jeśli użytkownik zaznaczył błędną
                    $label = " (BLEDNA)";
                }

                $box = $is_user_selected ? "[X] " : "[ ] ";
                $pdf->Cell(0, 6, iconv('UTF-8', 'windows-1250', "    " . $box . $ans['tresc'] . $label), 0, 1);
            }
            $pdf->Ln(5);
        }

        $pdf_filename = "test_res_" . $idp . "_" . $idt . "_" . time() . ".pdf";
        $pdf_path = "pdf/" . $pdf_filename;
        $pdf->Output('F', $pdf_path);
    } catch (Exception $e) {
        $pdf_filename = "";
    }
}

// 3. Zapis wyniku
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
            <p class="text-muted">Próg zaliczenia: <?php echo $test['prog_zaliczenia']; ?>%</p>
            <hr>
            <div class="mt-4">
                <?php if ($pdf_filename): ?>
                    <a href="pdf/<?php echo $pdf_filename; ?>" class="btn btn-success btn-lg w-100 mb-2 shadow" target="_blank">Pobierz Raport PDF</a>
                <?php else: ?>
                    <div class="alert alert-warning small">Raport PDF nie został wygenerowany (brak biblioteki lub błąd).</div>
                <?php endif; ?>
                <a href="index.php" class="btn btn-outline-secondary w-100">Wróć do strony głównej</a>
            </div>
        </div>
    </div>
</body>
</html>
