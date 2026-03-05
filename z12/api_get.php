<?php
/**
 * Plik: z12/api_get.php
 * Cel: Pobieranie danych pomiarowych (najnowszy rekord + historia).
 */
header('Content-Type: application/json');
require_once 'db_connect.php';

$response = [
    "latest" => null,
    "history" => []
];

// --- 1. POBIERANIE OSTATNIEGO REKORDU ---
$sql_latest = "SELECT * FROM pomiary ORDER BY id DESC LIMIT 1";
$res_latest = $conn->query($sql_latest);

if ($res_latest && $res_latest->num_rows > 0) {
    $response["latest"] = $res_latest->fetch_assoc();
}

// --- 2. POBIERANIE HISTORII (20 ostatnich rekordów, sortowanie rosnąco) ---
$sql_history = "SELECT * FROM (
                    SELECT * FROM pomiary 
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