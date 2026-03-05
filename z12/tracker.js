/**
 * Plik: z12/tracker.js
 * Cel: Zbieranie danych analitycznych z uwzględnieniem zgody na cookies.
 */
(function() {
    const COOKIE_NAME = "tm_cookies_accepted";

    const getTrackerData = (hasConsent) => {
        return {
            browser_info: navigator.userAgent,
            resolution: window.screen.width + "x" + window.screen.height,
            cookies_enabled: hasConsent ? 1 : 0,
            latitude: null,
            longitude: null
        };
    };

    const sendToAPI = (data) => {
        fetch('api_tracker.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(res => console.log("Tracker logged:", res.message))
        .catch(err => console.error("Tracker error:", err));
    };

    const startTracking = (hasConsent) => {
        const data = getTrackerData(hasConsent);
        
        if ("geolocation" in navigator && hasConsent) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    data.latitude = pos.coords.latitude;
                    data.longitude = pos.coords.longitude;
                    sendToAPI(data);
                },
                () => sendToAPI(data),
                { timeout: 3000 }
            );
        } else {
            sendToAPI(data);
        }
    };

    // Obsługa interfejsu
    document.addEventListener("DOMContentLoaded", () => {
        const toastEl = document.getElementById('cookieToast');
        const btnAccept = document.getElementById('acceptCookies');
        
        // Sprawdź czy już jest zgoda
        if (document.cookie.includes(COOKIE_NAME + "=true")) {
            if (toastEl) toastEl.classList.remove('show');
            startTracking(true);
        } else {
            // Brak ciasteczka - czekamy na akcję lub logujemy brak zgody przy pierwszej odsłonie
            if (btnAccept) {
                btnAccept.onclick = () => {
                    document.cookie = COOKIE_NAME + "=true; max-age=" + (60*60*24*30) + "; path=/";
                    const toast = bootstrap.Toast.getInstance(toastEl);
                    if (toast) toast.hide();
                    startTracking(true);
                    // Odśwież badge na home jeśli istnieje
                    const badge = document.getElementById('cookie-state');
                    if (badge) {
                        badge.innerText = 'Włączone (Zaakceptowano)';
                        document.getElementById('cookie-status-badge').classList.replace('bg-danger', 'bg-success');
                    }
                };
            }
            // Logujemy wizytę jako "bez cookies" jeśli użytkownik jeszcze nie kliknął
            startTracking(false);
        }
    });
})();