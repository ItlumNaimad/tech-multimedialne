<?php
/**
 * Plik: pages/scada.php (Wersja Docelowa)
 * Cel: Interaktywna wizualizacja procesów budynkowych zgodnie z punktami 8-11 instrukcji.
 */
?>
<style>
    /* Kontener Planu Budynku */
    .scada-viewport {
        position: relative;
        width: 100%;
        max-width: 900px;
        height: 500px;
        margin: 0 auto;
        background: #f8f9fa url('https://img.freepik.com/free-vector/modern-house-plan-with-furniture_23-2148292418.jpg') no-repeat center center;
        background-size: cover;
        border: 3px solid #343a40;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        overflow: hidden;
    }

    /* Warstwy Pomieszczeń (Punkt 10) */
    .room-overlay {
        position: absolute;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        transition: background-color 0.8s ease, border-color 0.5s;
        border: 1px dashed rgba(0,0,0,0.1);
        color: #000;
        text-shadow: 0 0 3px #fff;
    }
    
    /* Precyzyjne pozycjonowanie na rzucie (przykładowe) */
    #room-x3 { top: 5%;   left: 5%;   width: 25%; height: 40%; } /* Pokój 1 */
    #room-x4 { top: 5%;   left: 35%;  width: 30%; height: 40%; } /* Pokój 2 */
    #room-x5 { top: 50%;  left: 5%;   width: 25%; height: 40%; } /* Pokój 3 */
    
    /* Czujniki techniczne (x1, x2) */
    .sensor-node {
        position: absolute;
        background: rgba(255,255,255,0.9);
        border-radius: 20px;
        padding: 5px 12px;
        font-size: 0.8rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 10;
    }
    #sensor-x1 { top: 10%; right: 10%; border-left: 4px solid #dc3545; } /* Zasilanie */
    #sensor-x2 { top: 20%; right: 10%; border-left: 4px solid #0d6efd; } /* Powrót */

    /* Animacje Wentylacji (Punkt 9e) */
    .fan-slow { animation: fa-spin 3s linear infinite !important; }
    .fan-fast { animation: fa-spin 0.8s linear infinite !important; }
    
    /* Panel Alarmów (Punkt 9f) */
    .alarm-panel {
        background: rgba(255,255,255,0.85);
        border-radius: 10px;
        padding: 15px;
        backdrop-filter: blur(5px);
    }
    .status-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; font-weight: 500; }
    .status-row i { font-size: 1.2rem; min-width: 30px; text-align: center; }
</style>

<div class="row mb-4">
    <div class="col-md-8">
        <h2 class="fw-bold"><i class="fa-solid fa-house-laptop text-primary"></i> Wizualizacja Budynku SCADA</h2>
        <p class="text-muted">Monitoring parametrów życiowych i systemów bezpieczeństwa w czasie rzeczywistym.</p>
    </div>
    <div class="col-md-4 text-end">
        <div class="badge bg-dark p-2 mb-2"><i class="fa-regular fa-clock"></i> <span id="sync-time">--:--:--</span></div>
        <div class="d-flex justify-content-end gap-2">
            <a href="index.php?page=formularz" class="btn btn-sm btn-warning fw-bold">SYMULATOR</a>
            <a href="index.php?page=stats" class="btn btn-sm btn-success fw-bold">LOGI</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- GŁÓWNY PLAN (Punkt 8, 10) -->
    <div class="col-lg-9">
        <div class="scada-viewport">
            <!-- Warstwy pokoi (x3, x4, x5) -->
            <div id="room-x3" class="room-overlay"><span>Pokój 1</span><span id="val-x3">--</span></div>
            <div id="room-x4" class="room-overlay"><span>Pokój 2</span><span id="val-x4">--</span></div>
            <div id="room-x5" class="room-overlay"><span>Pokój 3</span><span id="val-x5">--</span></div>
            
            <!-- Czujniki instalacji (x1, x2) -->
            <div id="sensor-x1" class="sensor-node">Zasilanie: <b id="val-x1">--</b></div>
            <div id="sensor-x2" class="sensor-node">Powrót: <b id="val-x2">--</b></div>
        </div>
    </div>

    <!-- PANEL STATUSU (Punkt 9e, 9f) -->
    <div class="col-lg-3">
        <div class="alarm-panel shadow-sm h-100 border">
            <h5 class="border-bottom pb-2 mb-3 fw-bold">Stan Systemu</h5>
            
            <div class="status-row">
                <span><i class="fa-solid fa-fan" id="icon-vent"></i> Wentylacja</span>
                <span id="txt-vent" class="badge bg-secondary">OFF</span>
            </div>
            
            <div class="status-row">
                <span><i class="fa-solid fa-fire-extinguisher" id="icon-fire"></i> Pożar</span>
                <span id="txt-fire" class="badge bg-success">OK</span>
            </div>
            
            <div class="status-row">
                <span><i class="fa-solid fa-faucet-drip" id="icon-flood"></i> Zalanie</span>
                <span id="txt-flood" class="badge bg-success">OK</span>
            </div>
            
            <div class="status-row">
                <span><i class="fa-solid fa-biohazard" id="icon-gas"></i> Gaz</span>
                <span id="txt-gas" class="badge bg-success">OK</span>
            </div>
            
            <div class="status-row">
                <span><i class="fa-solid fa-smog" id="icon-co2"></i> CO2</span>
                <span id="txt-co2" class="badge bg-success">OK</span>
            </div>

            <hr>
            <div class="text-center mt-auto">
                <p class="small text-muted mb-1">Live Sync Status</p>
                <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
            </div>
        </div>
    </div>

    <!-- ZEGARY GOOGLE (Punkt 11) -->
    <div class="col-12 mt-4">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body p-0 d-flex justify-content-center">
                <div id="gauges_div"></div>
            </div>
        </div>
    </div>

    <!-- WYKRES LINIOWY (Punkt 8c) -->
    <div class="col-12 mt-2">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold"><i class="fa-solid fa-chart-line"></i> Historia temperatur x1-x5</div>
            <div class="card-body">
                <div style="height: 350px;"><canvas id="historyChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
    // --- LOGIKA KOLORÓW (Punkt 10) ---
    function getTempOverlay(t) {
        if (t < 10) return 'rgba(0, 123, 255, 0.4)'; // Niebieski
        if (t < 20) return 'rgba(40, 167, 69, 0.4)';  // Zielony
        if (t < 25) return 'transparent';             // Przezroczysty
        if (t < 30) return 'rgba(253, 126, 20, 0.4)'; // Pomarańczowy
        return 'rgba(220, 53, 69, 0.4)';             // Czerwony
    }

    // --- GOOGLE GAUGES (Punkt 11) ---
    google.charts.load('current', {'packages':['gauge']});
    let gaugeChart, gaugeData, gaugeOptions;

    google.charts.setOnLoadCallback(() => {
        gaugeData = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['x1', 0], ['x2', 0], ['x3', 0], ['x4', 0], ['x5', 0]
        ]);
        gaugeOptions = { 
            width: 800, height: 180, 
            redFrom: 35, redTo: 50, yellowFrom: 28, yellowTo: 35, 
            max: 50, minorTicks: 5 
        };
        gaugeChart = new google.visualization.Gauge(document.getElementById('gauges_div'));
        gaugeChart.draw(gaugeData, gaugeOptions);
    });

    // --- CHART.JS (Punkt 8c) ---
    const ctx = document.getElementById('historyChart').getContext('2d');
    const historyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [1,2,3,4,5].map(i => ({
                label: `Czujnik x${i}`,
                data: [],
                borderColor: `hsl(${(i-1)*60}, 70%, 50%)`,
                backgroundColor: `hsl(${(i-1)*60}, 70%, 50%, 0.1)`,
                borderWidth: 2,
                tension: 0.4,
                pointRadius: 2
            }))
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false,
            plugins: { tooltip: { mode: 'index', intersect: false } }
        }
    });

    // --- AJAX FETCH (Punkt 8, 9) ---
    async function updateSCADA() {
        try {
            const response = await fetch('api_get.php');
            const result = await response.json();

            if (result.latest) {
                const d = result.latest;
                document.getElementById('sync-time').innerText = d.datetime.split(' ')[1];

                // 1. Aktualizacja Temperatur (Plan + Gauges)
                for(let i=1; i<=5; i++) {
                    const val = parseFloat(d[`x${i}`]);
                    const valEl = document.getElementById(`val-x${i}`);
                    if (valEl) valEl.innerText = val.toFixed(1) + '°C';
                    
                    const roomEl = document.getElementById(`room-x${i}`);
                    if (roomEl) roomEl.style.backgroundColor = getTempOverlay(val);
                    
                    if (gaugeChart) gaugeData.setValue(i-1, 1, val);
                }
                if (gaugeChart) gaugeChart.draw(gaugeData, gaugeOptions);

                // 2. Logika Wentylacji (Punkt 9e)
                const vent = parseInt(d.ventilation);
                const vIcon = document.getElementById('icon-vent');
                const vTxt = document.getElementById('txt-vent');
                vIcon.className = 'fa-solid fa-fan';
                if (vent === 1) { vIcon.classList.add('fan-slow', 'text-info'); vTxt.innerText = 'WOLNO'; }
                else if (vent === 2) { vIcon.classList.add('fan-fast', 'text-primary'); vTxt.innerText = 'SZYBKO'; }
                else { vIcon.classList.add('text-muted'); vTxt.innerText = 'OFF'; }

                // 3. Logika Alarmów (Punkt 9f)
                const updateAlarm = (id, field, iconOn, iconOff) => {
                    const active = parseInt(d[field]) === 1;
                    const icon = document.getElementById('icon-' + id);
                    const txt = document.getElementById('txt-' + id);
                    icon.className = `fa-solid ${active ? iconOn + ' fa-beat text-danger' : iconOff + ' text-success'}`;
                    txt.innerText = active ? 'ALARM!' : 'OK';
                    txt.className = 'badge ' + (active ? 'bg-danger' : 'bg-success');
                };

                updateAlarm('fire', 'fire_alarm', 'fa-fire', 'fa-fire-extinguisher');
                updateAlarm('flood', 'flood', 'fa-water', 'fa-faucet-drip');
                updateAlarm('gas', 'gas', 'fa-skull-crossbones', 'fa-biohazard');
                updateAlarm('co2', 'co2', 'fa-smog', 'fa-leaf');
            }

            // 4. Aktualizacja Wykresu Historii
            if (result.history.length > 0) {
                historyChart.data.labels = result.history.map(r => r.datetime.split(' ')[1]);
                for(let i=1; i<=5; i++) {
                    historyChart.data.datasets[i-1].data = result.history.map(r => r[`x${i}`]);
                }
                historyChart.update('none');
            }
        } catch (err) { console.error("SCADA Fetch Error:", err); }
    }

    // Odświeżanie co 3 sekundy
    setInterval(updateSCADA, 3000);
    updateSCADA();
</script>