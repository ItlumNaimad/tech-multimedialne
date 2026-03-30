<?php
/**
 * Plik: test_submit.php
 * Cel: Sprawdzenie testu (dynamiczne odpowiedzi), generowanie PDF, wynik i próg.
 */
session_start();
require_once 'database/database.php';

// Sprawdź czy biblioteka FPDF istnieje, aby uniknąć Fatal Error i zerwania połączenia
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

// Pobierz dane testu (dla progu)
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
        'all_options' => $all_answers,
        'correct_ids' => $correct_ids
    ];
}

$percent = count($questions) > 0 ? ($total_points / count($questions)) * 100 : 0;
$passed = ($percent >= $test['prog_zaliczenia']);

// 1. Logowanie
$stmt_log = $pdo->prepare("INSERT INTO logi_aktywnosci (rola, id_uzytkownika, akcja) VALUES ('pracownik', ?, ?)");
$stmt_log->execute([$idp, "Ukończono test ID: $idt, wynik: $total_points, status: " . ($passed ? 'ZALICZONY' : 'NIEZALICZONY')]);

// 2. Generowanie PDF (tylko jeśli biblioteka istnieje)
$pdf_filename = "";
if ($has_fpdf) {
    try {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-2', 'Raport E-learning'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 7, iconv('UTF-8', 'ISO-8859-2', "Uzytkownik: " . $_SESSION['username']), 0, 1);
        $pdf->Cell(0, 7, iconv('UTF-8', 'ISO-8859-2', "Test: " . $test['nazwa']), 0, 1);
        $pdf->Cell(0, 7, iconv('UTF-8', 'ISO-8859-2', "Wynik: $total_points / " . count($questions) . " (" . round($percent) . "%)"), 0, 1);
        $pdf->Cell(0, 7, iconv('UTF-8', 'ISO-8859-2', "Status: " . ($passed ? 'ZALICZONY' : 'NIEZALICZONY')), 0, 1);
        $pdf->Ln(5);

        foreach ($report_data as $idx => $data) {
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->MultiCell(0, 7, iconv('UTF-8', 'ISO-8859-2', ($idx+1) . ". " . $data['q']));
            $pdf->SetFont('Arial', '', 10);
            
            foreach ($data['all_options'] as $ans) {
                $selected = in_array((string)$ans['idodp'], $data['user']);
                $correct = $ans['czy_poprawna'];
                
                $prefix = $selected ? "[X] " : "[ ] ";
                if ($correct) {
                    $pdf->SetTextColor(0, 128, 0); // Zielony dla poprawnych
                } else {
                    $pdf->SetTextColor(255, 0, 0); // Czerwony dla błędnych
                }
                $pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-2', "    " . $prefix . $ans['tresc']), 0, 1);
            }
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Arial', 'I', 9);
            $pdf->Cell(0, 6, iconv('UTF-8', 'ISO-8859-2', "Status: " . ($data['status'] ? "POPRAWNIE" : "BLEDNIE")), 0, 1);
            $pdf->Ln(4);
        }

        $pdf_filename = "test_res_" . $idp . "_" . $idt . "_" . time() . ".pdf";
        $pdf_path = "pdf/" . $pdf_filename;
        $pdf->Output('F', $pdf_path);
    } catch (Exception $e) {
        $pdf_filename = ""; // Błąd generowania
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
