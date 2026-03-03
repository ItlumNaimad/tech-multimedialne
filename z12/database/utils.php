<?php
/**
 * Plik: database/utils.php
 * Cel: Funkcje pomocnicze dla aplikacji.
 * Funkcjonalność: Dostarcza narzędzia do geolokalizacji na podstawie adresu IP.
 * Wykorzystane biblioteki: ipinfo.io (zewnętrzne API).
 * Sposób działania: Funkcja ip_details($ip) wysyła zapytanie do serwisu ipinfo.io i zwraca obiekt z danymi o lokalizacji (kraj, miasto, współrzędne).
 */
declare(strict_types=1);

function ip_details($ip) {
    // Używamy file_get_contents, aby odpytać zewnętrzny serwis API
    $json = @file_get_contents("http://ipinfo.io/{$ip}/geo");
    if ($json === FALSE) {
        return null; // Zwróć null, jeśli wystąpi błąd
    }
    // Dekodujemy odpowiedź JSON na obiekt PHP
    $details = json_decode($json);
    return $details;
}
?>