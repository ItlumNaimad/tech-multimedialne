<?php
/**
 * Plik: test_submit.php
 * Cel: Sprawdzenie testu, logowanie aktywności, generowanie PDF i zapis wyniku.
 */
session_start();
require_once 'database/database.php';
// Zakładamy, że fpdf.php jest dostępny w tym samym folderze lub przez include_path
require 'fpdf.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'pracownik') {
    die("Brak uprawnień.");
}

$idt = (int)$_POST['idt'];
$idp = $_SESSION['user_id'];
$answers_post = $_POST['ans'] ?? [];

// Pobierz poprawne odpowiedzi z bazy
$stmt = $pdo->prepare("SELECT * FROM pytania WHERE idt = ?");
$stmt->execute([$idt]);
$questions = $stmt->fetchAll();

$total_points = 0;
$report_data = [];

foreach ($questions as $q) {
    $q_id = $q['idpyt'];
    $user_choices = $answers_post[$q_id] ?? [];
    
    // Poprawne flagi z bazy
    $correct = [
        'a' => (bool)$q['a'],
        'b' => (bool)$q['b'],
        'c' => (bool)$q['c'],
        'd' => (bool)$q['d']
    ];
    
    // Sprawdzenie czy użytkownik wybrał DOKŁADNIE to co w bazie
    $is_correct = true;
    foreach (['a', 'b', 'c', 'd'] as $letter) {
        $selected = in_array($letter, $user_choices);
        if ($selected !== $correct[$letter]) {
            $is_correct = false;
            break;
        }
    }
    
    if ($is_correct) {
        $total_points++;
    }
    
    $report_data[] = [
        'q' => $q['tresc_pytania'],
        'status' => $is_correct,
        'user' => implode(', ', $user_choices),
        'correct' => implode(', ', array_keys(array_filter($correct)))
    ];
}

// 1. Logowanie aktywności
$stmt_log = $pdo->prepare("INSERT INTO logi_aktywnosci (rola, id_uzytkownika, akcja) VALUES ('pracownik', ?, ?)");
$stmt_log->execute([$idp, "Ukończono test ID: $idt, wynik: $total_points"]);

// 2. Generowanie PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-2', 'Raport z testu'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-2', "Uzytkownik: " . $_SESSION['username']), 0, 1);
$pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-2', "Wynik: $total_points / " . count($questions)), 0, 1);
$pdf->Ln(5);

foreach ($report_data as $idx => $data) {
    $pdf->SetTextColor(0, 0, 0);
    $pdf->MultiCell(0, 7, iconv('UTF-8', 'ISO-8859-2', ($idx+1) . ". " . $data['q']));
    
    if ($data['status']) {
        $pdf->SetTextColor(0, 128, 0); // Zielony
        $pdf->Cell(0, 7, iconv('UTF-8', 'ISO-8859-2', "Poprawnie! Odpowiedz: " . $data['user']), 0, 1);
    } else {
        $pdf->SetTextColor(255, 0, 0); // Czerwony
        $pdf->Cell(0, 7, iconv('UTF-8', 'ISO-8859-2', "Blednie. Twoja: " . ($data['user'] ?: "-") . " | Poprawna: " . $data['correct']), 0, 1);
    }
    $pdf->Ln(3);
}

$pdf_filename = "test_res_" . $idp . "_" . $idt . "_" . time() . ".pdf";
$pdf_path = "pdf/" . $pdf_filename;
$pdf->Output('F', $pdf_path);

// 3. Zapis do bazy wyników
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
        <div class="card shadow p-5" style="max-width: 500px; margin: auto;">
            <h2 class="text-primary mb-4">Test zakończony!</h2>
            <p class="fs-4">Twój wynik: <strong><?php echo $total_points; ?> / <?php echo count($questions); ?></strong></p>
            <hr>
            <div class="mt-4">
                <a href="<?php echo $pdf_path; ?>" class="btn btn-success btn-lg w-100 mb-2" target="_blank">Pobierz Raport PDF</a>
                <a href="index.php" class="btn btn-outline-secondary w-100">Wróć do strony głównej</a>
            </div>
        </div>
    </div>
</body>
</html>
