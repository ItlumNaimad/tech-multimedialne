/**
 * Plik: z12/tracker.js
 * Cel: Zbieranie danych analitycznych o gościu (geolokalizacja, ekran, cookies).
 */
(function() {
    const trackerData = {
        browser_info: navigator.userAgent,
        resolution: window.screen.width + "x" + window.screen.height,
        cookies_enabled: navigator.cookieEnabled ? 1 : 0,
        latitude: null,
        longitude: null
    };

    const sendData = (data) => {
        fetch('api_tracker.php', {
            method: 'POST',
            headers: { 'Content-Type: application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(res => console.log("Tracker status:", res.message))
        .catch(err => console.error("Tracker error:", err));
    };

    // Próba pobrania geolokalizacji
    if ("geolocation" in navigator) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                trackerData.latitude = position.coords.latitude;
                trackerData.longitude = position.coords.longitude;
                sendData(trackerData);
            },
            (error) => {
                console.warn("Geolocation denied or error:", error.message);
                sendData(trackerData); // Wysyłamy bez GPS
            },
            { timeout: 5000 }
        );
    } else {
        sendData(trackerData);
    }
})();