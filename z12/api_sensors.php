<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// Odbieramy parametry czujników (v0..v5) - GET lub POST
$v0 = $_REQUEST['v0'] ?? null;
$v1 = $_REQUEST['v1'] ?? null;
$v2 = $_REQUEST['v2'] ?? null;
$v3 = $_REQUEST['v3'] ?? null;
$v4 = $_REQUEST['v4'] ?? null;
$v5 = $_REQUEST['v5'] ?? null;

// --- TRYB ZAPISU (jeśli przesłano parametry) ---
if ($v0 !== null && $v1 !== null) {
    // Walidacja danych (konwersja na float)
    $v0 = (float)$v0; $v1 = (float)$v1; $v2 = (float)$v2;
    $v3 = (float)$v3; $v4 = (float)$v4; $v5 = (float)$v5;

    // Prepared Statement do zapisu
    $sql = "INSERT INTO vmeter (v0, v1, v2, v3, v4, v5) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("dddddd", $v0, $v1, $v2, $v3, $v4, $v5);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Dane zapisane pomyślnie"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Błąd zapisu danych"]);
    }
    $stmt->close();

// --- TRYB ODCZYTU (jeśli brak parametrów - zwracamy JSON dla wykresu) ---
} else {
    // Pobieramy 20 ostatnich rekordów, sortujemy chronologicznie
    $sql = "SELECT * FROM (
                SELECT id, recorded, v0, v1, v2, v3, v4, v5 
                FROM vmeter 
                ORDER BY recorded DESC 
                LIMIT 20
            ) sub 
            ORDER BY recorded ASC";
    
    $result = $conn->query($sql);
    $data = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    echo json_encode($data);
}

$conn->close();
?>