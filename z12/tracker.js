/**
 * Tracker.js - Moduł analityczny dla portalu SCADA
 * Pobiera dane o przeglądarce, rozdzielczości i geolokalizacji.
 */
/**
 * Plik: tracker.js
 * Cel: Skrypt analityczny po stronie klienta.
 * Funkcjonalność: Zbieranie informacji o sesji użytkownika i przesyłanie ich do bazy.
 * Wykorzystane biblioteki: Geolocation API (natywne przeglądarki).
 * Sposób działania: 
 *   - Pobiera User Agent, rozdzielczość ekranu.
 *   - Wysyła prośbę o dostęp do lokalizacji GPS.
 *   - Przesyła zebrane dane metodą POST do api_tracker.php przy użyciu Fetch API.
 */
(function() {
    function sendTrackingData(lat = null, lon = null) {
        const payload = {
            latitude: lat,
            longitude: lon,
            browser_info: navigator.userAgent,
            resolution: `${screen.width}x${screen.height}`
        };

        fetch('api_tracker.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => console.log('Analytics saved:', data))
        .catch(err => console.error('Analytics error:', err));
    }

    // Próba pobrania geolokalizacji
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                sendTrackingData(position.coords.latitude, position.coords.longitude);
            },
            (error) => {
                console.warn("Geolocation denied, sending data without coordinates.");
                sendTrackingData(); // Wyślij bez GPS jeśli użytkownik odmówił
            }
        );
    } else {
        sendTrackingData(); // Brak wsparcia dla geolokalizacji
    }
})();