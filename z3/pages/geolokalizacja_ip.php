<h3>Zadanie 11: Geolokalizacja IP (ipinfo.io)</h3>
<p>Testowanie skryptu geolokalizacji na podstawie Twojego aktualnego adresu IP.</p>

<pre class="bg-dark text-white p-2 rounded">
<?php
// Upewniamy się, że mamy adres IP, nawet jeśli jest za proxy
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
}

// Funkcja z instrukcji
function ip_details($ip) {
    // Używamy file_get_contents, aby odpytać zewnętrzny serwis API
    $json = @file_get_contents("http://ipinfo.io/{$ip}/geo");
    if ($json === FALSE) {
        return null; // Zwróć null, jeśli wystąpi błąd (np. timeout)
    }
    // Dekodujemy odpowiedź JSON na obiekt PHP
    $details = json_decode($json);
    return $details;
}

echo "<b>Odpytywanie serwisu ipinfo.io dla IP: " . htmlspecialchars($ipaddress) . "</b>\n\n";

// Wywołujemy funkcję
$details = ip_details($ipaddress);

if ($details) {
    // Wyświetlamy kluczowe dane
    echo "IP: " . htmlspecialchars($details->ip) . "\n";
    echo "Region: " . htmlspecialchars($details->region) . "\n";
    echo "Kraj: " . htmlspecialchars($details->country) . "\n";
    echo "Miasto: " . htmlspecialchars($details->city) . "\n";
    echo "Współrzędne (lokalizacja): " . htmlspecialchars($details->loc) . "\n";
    echo "\n<b>--- Pełna odpowiedź JSON ---</b>\n";
    print_r($details);
} else {
    echo "Nie udało się pobrać danych z ipinfo.io. Serwis może być niedostępny lub blokować zapytania.";
}
?>
</pre>
