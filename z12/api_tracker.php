<?php
/**
 * Plik: z12/api_tracker.php
 * Cel: Rejestrowanie danych analitycznych (geolokalizacja, parametry ekranu).
 */
header('Content-Type: application/json');
require_once 'db_connect.php';

// Odbieramy dane JSON z body żądania (wysyłane przez tracker.js)
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Brak danych JSON"]);
    exit();
}

// Dane pobierane z żądania (JS)
$lat = $data['latitude'] ?? null;
$lon = $data['longitude'] ?? null;
$browser_info = $data['browser_info'] ?? $_SERVER['HTTP_USER_AGENT'];
$resolution = $data['resolution'] ?? 'Nieznana';
$cookies_enabled = isset($data['cookies_enabled']) ? (int)$data['cookies_enabled'] : 0;

// Dane pobierane po stronie serwera (PHP)
$ip_address = $_SERVER['REMOTE_ADDR'];

// Zapisujemy do tabeli visitor_logs (zgodnie z nowym schematem SQL)
$sql = "INSERT INTO visitor_logs (ip_address, latitude, longitude, browser_info, resolution, cookies_enabled) 
        VALUES (?, ?, ?, ?, ?, ?)";

try {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sddssi", $ip_address, $lat, $lon, $browser_info, $resolution, $cookies_enabled);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Ślad wizyty zapisany"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Błąd zapisu w bazie danych"]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

$conn->close();
?>