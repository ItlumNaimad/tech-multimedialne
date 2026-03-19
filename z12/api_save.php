<?php
/**
 * Plik: z12/api_save.php
 * Cel: Zapis nowych danych z czujników do tabeli 'vmeter' (Zintegrowana wersja).
 */
header('Content-Type: application/json');
require_once 'db_connect.php';

// Odbieramy parametry (metoda POST)
$x1 = $_POST['x1'] ?? null;
$x2 = $_POST['x2'] ?? null;
$x3 = $_POST['x3'] ?? null;
$x4 = $_POST['x4'] ?? null;
$x5 = $_POST['x5'] ?? null;

$ventilation = $_POST['ventilation'] ?? 0;
$fire_alarm  = $_POST['fire_alarm']  ?? 0;
$flood       = $_POST['flood']       ?? 0;
$gas         = $_POST['gas']         ?? 0;
$co2         = $_POST['co2']         ?? 0;

if ($x1 === null || $x2 === null || $x3 === null || $x4 === null || $x5 === null) {
    echo json_encode(["status" => "error", "message" => "Brak wymaganych danych"]);
    exit();
}

// Konwersja typów (zapisujemy x1->v0, x2->v1 itd.)
$v0 = (float)$x1; $v1 = (float)$x2; $v2 = (float)$x3; $v3 = (float)$x4; $v4 = (float)$x5;
$v5 = 0; // Pole v5 zostawiamy puste dla formularza WWW

// Przygotowanie zapytania INSERT do vmeter
$sql = "INSERT INTO vmeter (v0, v1, v2, v3, v4, v5, ventilation, fire_alarm, flood, gas, co2) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ddddddiiiii", $v0, $v1, $v2, $v3, $v4, $v5, $ventilation, $fire_alarm, $flood, $gas, $co2);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Dane zapisane w vmeter", "id" => $conn->insert_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Błąd zapisu danych: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>