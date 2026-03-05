<?php
/**
 * Plik: z12/api_save.php
 * Cel: Zapis nowych danych z czujników do tabeli 'pomiary'.
 */
header('Content-Type: application/json');
require_once 'db_connect.php';

// Odbieramy parametry (metoda POST)
$x1 = $_POST['x1'] ?? null;
$x2 = $_POST['x2'] ?? null;
$x3 = $_POST['x3'] ?? null;
$x4 = $_POST['x4'] ?? null;
$x5 = $_POST['x5'] ?? null;

$ventilation = $_POST['ventilation'] ?? null;
$fire_alarm  = $_POST['fire_alarm']  ?? null;
$flood       = $_POST['flood']       ?? null;
$gas         = $_POST['gas']         ?? null;
$co2         = $_POST['co2']         ?? null;

// Prosta walidacja - wymagamy co najmniej temperatur
if ($x1 === null || $x2 === null || $x3 === null || $x4 === null || $x5 === null) {
    echo json_encode(["status" => "error", "message" => "Brak wymaganych danych temperatur (x1-x5)"]);
    exit();
}

// Konwersja typów
$x1 = (float)$x1; $x2 = (float)$x2; $x3 = (float)$x3; $x4 = (float)$x4; $x5 = (float)$x5;
$ventilation = (int)$ventilation;
$fire_alarm  = (int)$fire_alarm;
$flood       = (int)$flood;
$gas         = (int)$gas;
$co2         = (int)$co2;

// Przygotowanie zapytania INSERT
$sql = "INSERT INTO pomiary (x1, x2, x3, x4, x5, ventilation, fire_alarm, flood, gas, co2) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("dddddiiiii", $x1, $x2, $x3, $x4, $x5, $ventilation, $fire_alarm, $flood, $gas, $co2);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Pomiar zapisany pomyślnie", "id" => $conn->insert_id]);
} else {
    echo json_encode(["status" => "error", "message" => "Błąd zapisu danych: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>