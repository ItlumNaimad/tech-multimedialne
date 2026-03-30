<?php
/**
 * Plik: test_submit.php
 * Cel: Sprawdzenie testu, generowanie PDF (Wersja STABILNA - FPDF + iconv).
 */
session_start();
require_once 'database/database.php';

// Ważne: FPDF szuka folderu 'font/' w katalogu roboczym, jeśli nie jest zdefiniowany
if(!defined('FPDF_FONTPATH')) define('FPDF_FONTPATH', __DIR__ . '/font/');

require_once 'fpdf.php'; // Biblioteka standardowa

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

// GENEROWANIE PDF - METODA STABILNA (ISO-8859-2)
$pdf_filename = "test_res_" . $idp . "_" . $idt . "_" . time() . ".pdf";
$pdf_path = "pdf/" . $pdf_filename;

// Upewnij się, że katalog pdf istnieje i jest zapisywalny
if (!file_exists('pdf')) {
    mkdir('pdf', 0777, true);
}

// Funkcja pomocnicza do konwersji polskich znaków dla FPDF (Windows-1250)
function pl($text) {
    if (function_exists('iconv')) {
        // Konwersja na Windows-1250, która najlepiej współpracuje z czcionką Helvetica w FPDF
        return iconv('UTF-8', 'windows-1250//TRANSLIT', $text);
    }
    return $text;
}

$pdf_error_msg = "";

try {
    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();
    
    // Używamy czcionki HELVETICA (pliki helvetica.php i helveticab.php są obecne w Twoim folderze font/)
    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->Cell(0, 10, pl("RAPORT Z TESTU: " . $test['nazwa']), 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Helvetica', '', 11);
    $pdf->Cell(0, 7, pl("Użytkownik: " . $_SESSION['username']), 0, 1);
    $pdf->Cell(0, 7, pl("Data: " . date("Y-m-d H:i:s")), 0, 1);
    $pdf->Cell(0, 7, pl("Wynik: $total_points / " . count($questions) . " (" . round($percent) . "%)"), 0, 1);
    
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
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->MultiCell(0, 7, pl(($idx+1) . ". " . $data['q']), 0, 'L');
        
        $pdf->SetFont('Helvetica', '', 10);
        foreach ($data['all_options'] as $ans) {
            $is_user_selected = in_array((string)$ans['idodp'], $data['user']);
            $is_correct_ans = $ans['czy_poprawna'];
            
            if ($is_user_selected && $is_correct_ans) {
                $pdf->SetTextColor(0, 128, 0);
                $tag = " [POPRAWNA]";
            } elseif ($is_user_selected && !$is_correct_ans) {
                $pdf->SetTextColor(255, 0, 0);
                $tag = " [BLEDNA]";
            } elseif (!$is_user_selected && $is_correct_ans) {
                $pdf->SetTextColor(255, 0, 0);
                $tag = " [POPRAWNA - POMINIETO]";
            } else {
                $pdf->SetTextColor(100, 100, 100);
                $tag = "";
            }

            $box = $is_user_selected ? "[X] " : "[ ] ";
            $pdf->Cell(10);
            $pdf->MultiCell(0, 6, pl($box . $ans['tresc'] . $tag), 0, 'L');
            $pdf->SetTextColor(0, 0, 0);
        }
        $pdf->Ln(3);
    }

    // Sprawdzamy czy ścieżka zapisu jest dostępna
    if (!is_writable('pdf')) {
        throw new Exception("Katalog 'pdf/' nie jest zapisywalny.");
    }

    $pdf->Output('F', $pdf_path);
} catch (Exception $e) {
    $pdf_error_msg = $e->getMessage();
    $pdf_filename = "";
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
                    <div class="alert alert-danger small">
                        Raport PDF nie został wygenerowany.<br>
                        <?php if($pdf_error_msg) echo "<strong>Błąd:</strong> " . htmlspecialchars($pdf_error_msg); ?>
                    </div>
                <?php endif; ?>
                <a href="index.php" class="btn btn-outline-secondary w-100">Wróć do strony głównej</a>
            </div>
        </div>
    </div>
</body>
</html>
