<?php
/**
 * Plik: pages/scada.php (Wersja 3.0 - Techniczna Wizualizacja)
 * Cel: Interaktywny plan budynku z dynamicznymi elementami automatyki.
 */
?>
<style>
    /* Kontener Planu Budynku - Styl Techniczny */
    .scada-viewport {
        position: relative;
        width: 100%;
        max-width: 900px;
        height: 550px;
        margin: 0 auto;
        /* Techniczny podkład (Blueprints) */
        background: #1a1a1a url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
        border: 4px solid #444;
        border-radius: 15px;
        box-shadow: inset 0 0 50px rgba(0,0,0,0.5), 0 10px 30px rgba(0,0,0,0.3);
        overflow: hidden;
    }

    /* Siatka techniczna w tle */
    .scada-viewport::before {
        content: "";
        position: absolute;
        width: 100%; height: 100%;
        background-image: linear-gradient(rgba(0, 255, 255, 0.05) 1px, transparent 1px), 
                          linear-gradient(90, rgba(0, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 30px 30px;
        pointer-events: none;
    }

    /* Warstwy Pomieszczeń (Punkt 10) */
    .room-overlay {
        position: absolute;
        border: 2px solid rgba(0, 255, 255, 0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: all 0.6s ease;
        color: #fff;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
    }
    
    #room-x3 { top: 10%; left: 5%;  width: 40%; height: 35%; } /* Biuro A */
    #room-x4 { top: 10%; left: 50%; width: 45%; height: 35%; } /* Biuro B */
    #room-x5 { top: 55%; left: 5%;  width: 90%; height: 35%; } /* Hala / Serwerownia */

    /* Ikony urządzeń na planie */
    .device-icon {
        position: absolute;
        font-size: 2rem;
        z-index: 20;
        filter: drop-shadow(0 0 5px rgba(0,0,0,0.5));
        transition: all 0.4s;
    }
    
    /* Pozycjonowanie urządzeń */
    #map-vent { top: 15%; left: 42%; }
    #map-fire { top: 15%; right: 8%; }
    #map-flood { bottom: 15%; left: 10%; }
    #map-gas { bottom: 15%; right: 10%; }

    /* Pływające etykiety temperatur */
    .temp-badge {
        background: rgba(0,0,0,0.7);
        padding: 2px 8px;
        border-radius: 5px;
        font-family: monospace;
        color: #0ff;
        border: 1px solid #0ff;
        margin-top: 5px;
    }

    /* Animacje (Punkt 9e, 9f) */
    .fan-spin-slow { animation: fa-spin 3s linear infinite; }
    .fan-spin-fast { animation: fa-spin 0.8s linear infinite; }
    .alarm-blink { animation: fa-beat 1s infinite; color: #ff4d4d !important; }
    .status-ok { color: #00ff88; opacity: 0.8; }
</style>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-microchip text-primary"></i> System SCADA v3.0</h2>
        <p class="text-muted">Interaktywny plan budynku z monitoringiem sensorów x1-x5.</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group shadow-sm">
            <a href="index.php?page=formularz" class="btn btn-warning"><i class="fa-solid fa-sliders"></i> Symulator</a>
            <a href="index.php?page=stats" class="btn btn-dark"><i class="fa-solid fa-list-ul"></i> Logi</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- GŁÓWNY PLAN Z URZĄDZENIAMI -->
    <div class="col-lg-12">
        <div class="scada-viewport">
            <!-- Warstwy Pomieszczeń -->
            <div id="room-x3" class="room-overlay">
                <span>Strefa Robocza 1</span>
                <div class="temp-badge" id="val-x3">--.- °C</div>
            </div>
            <div id="room-x4" class="room-overlay">
                <span>Strefa Robocza 2</span>
                <div class="temp-badge" id="val-x4">--.- °C</div>
            </div>
            <div id="room-x5" class="room-overlay">
                <span>Centrum Danych / HVAC</span>
                <div class="temp-badge" id="val-x5">--.- °C</div>
            </div>

            <!-- Ikony Urządzeń (Punkt 9) -->
            <i class="fa-solid fa-fan device-icon text-muted" id="map-vent" title="Wentylacja"></i>
            <i class="fa-solid fa-fire-extinguisher device-icon status-ok" id="map-fire" title="P.Poż"></i>
            <i class="fa-solid fa-droplets device-icon status-ok" id="map-flood" title="Zalanie"></i>
            <i class="fa-solid fa-biohazard device-icon status-ok" id="map-gas" title="Gaz"></i>

            <!-- Czujniki techniczne (Zasilanie/Powrót) -->
            <div style="position:absolute; top: 2%; right: 2%;" class="text-white small">
                <div class="mb-1"><i class="fa-solid fa-arrow-right text-danger"></i> Zasilanie (x1): <b id="val-x1" class="text-info">--</b></div>
                <div><i class="fa-solid fa-arrow-left text-primary"></i> Powrót (x2): <b id="val-x2" class="text-info">--</b></div>
            </div>
        </div>
    </div>

    <!-- DOLNY PANEL: ZEGARY I WYKRES -->
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold small">AKTUALNE PARAMETRY (GAUGES)</div>
            <div class="card-body d-flex justify-content-center align-items-center bg-light">
                <div id="gauges_div"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-dark text-white fw-bold small">HISTORIA TEMPERATUR (CHART.JS)</div>
            <div class="card-body">
                <div style="height: 200px;"><canvas id="historyChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Logika kolorów warstw (Punkt 10)
    function getTempOverlay(t) {
        if (t < 10) return 'rgba(0, 123, 255, 0.3)'; 
        if (t < 20) return 'rgba(40, 167, 69, 0.3)';  
        if (t < 25) return 'transparent';             
        if (t < 30) return 'rgba(253, 126, 20, 0.3)'; 
        return 'rgba(220, 53, 69, 0.4)';             
    }

    // Google Gauges
    google.charts.load('current', {'packages':['gauge']});
    let gaugeChart, gaugeData, gaugeOptions;
    google.charts.setOnLoadCallback(() => {
        gaugeData = google.visualization.arrayToDataTable([
            ['Label', 'Value'], ['x1', 0], ['x2', 0], ['x3', 0], ['x4', 0], ['x5', 0]
        ]);
        gaugeOptions = { width: 400, height: 150, redFrom: 35, redTo: 50, yellowFrom: 28, yellowTo: 35, max: 50 };
        gaugeChart = new google.visualization.Gauge(document.getElementById('gauges_div'));
        gaugeChart.draw(gaugeData, gaugeOptions);
    });

    // Chart.js
    const ctx = document.getElementById('historyChart').getContext('2d');
    const historyChart = new Chart(ctx, {
        type: 'line',
        data: { labels: [], datasets: [1,2,3,4,5].map(i => ({
            label: `x${i}`, data: [], borderColor: `hsl(${(i-1)*60}, 70%, 50%)`, tension: 0.4, pointRadius: 0, borderWidth: 2
        }))},
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });

    async function updateSCADA() {
        try {
            const response = await fetch('api_get.php');
            const result = await response.json();

            if (result.latest) {
                const d = result.latest;

                // 1. Temperatury
                for(let i=1; i<=5; i++) {
                    const val = parseFloat(d[`x${i}`]);
                    const valEl = document.getElementById(`val-x${i}`);
                    if (valEl) valEl.innerText = val.toFixed(1) + ' °C';
                    
                    const roomEl = document.getElementById(`room-x${i}`);
                    if (roomEl) roomEl.style.backgroundColor = getTempOverlay(val);
                    
                    if (gaugeChart) gaugeData.setValue(i-1, 1, val);
                }
                if (gaugeChart) gaugeChart.draw(gaugeData, gaugeOptions);

                // 2. Wentylacja (Ikona na mapie)
                const vent = parseInt(d.ventilation);
                const vIcon = document.getElementById('map-vent');
                vIcon.className = 'fa-solid fa-fan device-icon';
                if (vent === 1) vIcon.classList.add('fan-spin-slow', 'text-info');
                else if (vent === 2) vIcon.classList.add('fan-spin-fast', 'text-primary');
                else vIcon.classList.add('text-muted');

                // 3. Alarmy (Ikony na mapie)
                const checkAlarm = (id, field, iconOn, iconOff) => {
                    const active = parseInt(d[field]) === 1;
                    const el = document.getElementById('map-' + id);
                    el.className = `fa-solid ${active ? iconOn + ' alarm-blink' : iconOff + ' status-ok'} device-icon`;
                };

                checkAlarm('fire', 'fire_alarm', 'fa-fire', 'fa-fire-extinguisher');
                checkAlarm('flood', 'flood', 'fa-water', 'fa-droplets');
                checkAlarm('gas', 'gas', 'fa-skull-crossbones', 'fa-biohazard');
            }

            if (result.history.length > 0) {
                historyChart.data.labels = result.history.map(r => r.datetime.split(' ')[1]);
                for(let i=1; i<=5; i++) historyChart.data.datasets[i-1].data = result.history.map(r => r[`x${i}`]);
                historyChart.update('none');
            }
        } catch (e) { console.error(e); }
    }

    setInterval(updateSCADA, 3000);
    updateSCADA();
</script>