<?php
/**
 * Plik: api_tracker.php
 * Cel: Endpoint API dla modułu analitycznego tracker.js.
 * Funkcjonalność: Rejestruje dane o wizycie użytkownika, w tym geolokalizację i parametry techniczne.
 * Wykorzystane biblioteki: Brak (MySQLi).
 * Sposób działania: Odbiera dane w formacie JSON z body żądania, dekoduje je i zapisuje (szerokość/długość geograficzną, info o przeglądarce i rozdzielczość) w tabeli 'visitor_logs'.
 */
header('Content-Type: application/json');
require_once 'db_connect.php';

// Odbieramy dane JSON z body żądania
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Brak danych JSON"]);
    exit();
}

$lat = $data['latitude'] ?? null;
$lon = $data['longitude'] ?? null;
$browser_info = $data['browser_info'] ?? 'Nieznana';
$resolution = $data['resolution'] ?? 'Nieznana';

// Zapisujemy do tabeli visitor_logs
try {
    $stmt = $conn->prepare("INSERT INTO visitor_logs (latitude, longitude, browser_info, resolution, recorded) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ddss", $lat, $lon, $browser_info, $resolution);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Odwiedziny odnotowane"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Błąd zapisu w bazie"]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>