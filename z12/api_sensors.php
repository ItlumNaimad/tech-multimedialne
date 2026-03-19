<?php
/**
 * Plik: api_sensors.php
 * Cel: Dwukierunkowy interfejs API dla Arduino (system vmeter).
 */
header('Content-Type: application/json');
require_once 'db_connect.php';

// Odbieramy parametry czujników (v0..v5)
$v0 = $_REQUEST['v0'] ?? null;
$v1 = $_REQUEST['v1'] ?? null;
$v2 = $_REQUEST['v2'] ?? null;
$v3 = $_REQUEST['v3'] ?? null;
$v4 = $_REQUEST['v4'] ?? null;
$v5 = $_REQUEST['v5'] ?? null;

// Odbieramy alarmy (opcjonalnie)
$vent = $_REQUEST['ventilation'] ?? 0;
$fire = $_REQUEST['fire_alarm']  ?? 0;
$flood= $_REQUEST['flood']       ?? 0;
$gas  = $_REQUEST['gas']         ?? 0;
$co2  = $_REQUEST['co2']         ?? 0;

// --- TRYB ZAPISU (Dla Arduino) ---
if ($v0 !== null && $v1 !== null) {
    $v0 = (float)$v0; $v1 = (float)$v1; $v2 = (float)$v2;
    $v3 = (float)$v3; $v4 = (float)$v4; $v5 = (float)$v5;
    $vent = (int)$vent; $fire = (int)$fire; $flood = (int)$flood; 
    $gas = (int)$gas; $co2 = (int)$co2;

    $sql = "INSERT INTO vmeter (v0, v1, v2, v3, v4, v5, ventilation, fire_alarm, flood, gas, co2) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddddddiiiii", $v0, $v1, $v2, $v3, $v4, $v5, $vent, $fire, $flood, $gas, $co2);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Arduino data saved"]);
    } else {
        echo json_encode(["status" => "error", "message" => "DB error: " . $stmt->error]);
    }
    $stmt->close();

// --- TRYB ODCZYTU (JSON dla wykresu) ---
} else {
    $sql = "SELECT id, recorded, v0, v1, v2, v3, v4, v5 FROM vmeter ORDER BY id DESC LIMIT 20";
    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode(array_reverse($data));
}

$conn->close();
?>