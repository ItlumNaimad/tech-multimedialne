<?php
/**
 * Plik: z12/api_get.php
 * Cel: Pobieranie danych pomiarowych (nowa wersja zintegrowana z vmeter / Arduino).
 */
header('Content-Type: application/json');
require_once 'db_connect.php';

$response = [
    "latest" => null,
    "history" => []
];

// --- 1. POBIERANIE OSTATNIEGO REKORDU Z VMETER ---
// Mapujemy v0..v4 na x1..x5 dla kompatybilności wstecznej z interfejsem SCADA
$sql_latest = "SELECT 
                id, recorded as datetime, 
                v0 as x1, v1 as x2, v2 as x3, v3 as x4, v4 as x5, 
                ventilation, fire_alarm, flood, gas, co2 
               FROM vmeter 
               ORDER BY id DESC LIMIT 1";

$res_latest = $conn->query($sql_latest);

if ($res_latest && $res_latest->num_rows > 0) {
    $response["latest"] = $res_latest->fetch_assoc();
}

// --- 2. POBIERANIE HISTORII (20 ostatnich rekordów) ---
$sql_history = "SELECT * FROM (
                    SELECT 
                        id, recorded as datetime, 
                        v0 as x1, v1 as x2, v2 as x3, v3 as x4, v4 as x5 
                    FROM vmeter 
                    ORDER BY id DESC 
                    LIMIT 20
                ) sub 
                ORDER BY id ASC";

$res_history = $conn->query($sql_history);

if ($res_history) {
    while ($row = $res_history->fetch_assoc()) {
        $response["history"][] = $row;
    }
}

echo json_encode($response);

$conn->close();
?>