<?php
/**
 * Plik: pages/scada.php (Wersja modułowa)
 * Cel: Widok monitorowania SCADA dla routera index.php.
 */
?>
<style>
    .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .gauge-container { display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; }
    
    /* Stylistyka Planu Budynku */
    .building-plan {
        background: #fff;
        border: 2px solid #333;
        border-radius: 8px;
        position: relative;
        height: 350px;
        width: 100%;
        overflow: hidden;
        background-image: linear-gradient(#eee 1px, transparent 1px), linear-gradient(90, #eee 1px, transparent 1px);
        background-size: 20px 20px;
    }
    .room {
        position: absolute;
        border: 2px solid #444;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: background-color 0.5s ease;
        font-weight: bold;
        text-align: center;
        padding: 5px;
    }
    .room-label { font-size: 0.75rem; text-transform: uppercase; color: #333; margin-bottom: 2px; }
    .room-value { font-size: 1.2rem; }

    /* Rozmieszczenie pokoi na planie */
    #room-v0 { top: 10%; left: 5%;  width: 25%; height: 40%; }
    #room-v1 { top: 10%; left: 30%; width: 25%; height: 40%; }
    #room-v2 { top: 10%; left: 55%; width: 40%; height: 40%; } 
    #room-v3 { top: 50%; left: 5%;  width: 50%; height: 40%; }
    #room-v4 { top: 50%; left: 55%; width: 20%; height: 40%; }
    #room-v5 { top: 50%; left: 75%; width: 20%; height: 40%; }
</style>

<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="fw-bold mb-0"><i class="bi bi-speedometer2 text-primary"></i> System Monitorowania SCADA</h2>
        <div class="btn-group">
            <a href="index.php?page=stats" class="btn btn-outline-primary btn-sm"><i class="bi bi-bar-chart-line"></i> Statystyki Wizyt</a>
        </div>
    </div>
    <hr class="mt-3">
</div>

<div class="row g-4">
    <!-- SEKCJA SYMULATORA -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-input-cursor-text"></i> Symulator Wejść (z12a)</h5>
            </div>
            <div class="card-body">
                <form id="simulator-form">
                    <div class="row g-2">
                        <?php for($i=0; $i<=5; $i++): ?>
                        <div class="col-6 mb-2">
                            <label class="small text-muted">Czujnik V<?php echo $i; ?></label>
                            <input type="number" name="v<?php echo $i; ?>" class="form-control" value="<?php echo rand(15, 35); ?>" step="0.1" required>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3" id="btn-send">
                        <i class="bi bi-send"></i> Wyślij do bazy
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- SEKCJA PLANU BUDYNKU -->
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-map"></i> Wizualizacja Przestrzenna</h5>
            </div>
            <div class="card-body">
                <div class="building-plan">
                    <div id="room-v0" class="room"><span class="room-label">Biuro 1</span><span class="room-value" id="val-v0">--</span></div>
                    <div id="room-v1" class="room"><span class="room-label">Biuro 2</span><span class="room-value" id="val-v1">--</span></div>
                    <div id="room-v2" class="room"><span class="room-label">Serwerownia</span><span class="room-value" id="val-v2">--</span></div>
                    <div id="room-v3" class="room"><span class="room-label">Hol Główny</span><span class="room-value" id="val-v3">--</span></div>
                    <div id="room-v4" class="room"><span class="room-label">Magazyn</span><span class="room-value" id="val-v4">--</span></div>
                    <div id="room-v5" class="room"><span class="room-label">Zaplecze</span><span class="room-value" id="val-v5">--</span></div>
                </div>
                <div class="mt-3 d-flex justify-content-center gap-3 small text-muted">
                    <span><i class="bi bi-square-fill text-primary"></i> < 10°C</span>
                    <span><i class="bi bi-square-fill text-success"></i> < 20°C</span>
                    <span><i class="bi bi-square text-dark"></i> 20-25°C</span>
                    <span><i class="bi bi-square-fill text-warning"></i> < 30°C</span>
                    <span><i class="bi bi-square-fill text-danger"></i> > 30°C</span>
                </div>
            </div>
        </div>
    </div>

    <!-- SEKCJA ZEGARÓW -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Wartości Aktualne (Gauges)</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div id="gauges_div" class="gauge-container"></div>
            </div>
        </div>
    </div>

    <!-- SEKCJA WYKRESU -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Historia Pomiarów</h5>
                <span class="badge bg-success" id="sync-status">Live Sync: ON</span>
            </div>
            <div class="card-body">
                <div style="height: 300px;"><canvas id="historyChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<script>
    function getTempColor(t) {
        if (t < 10) return 'rgba(0, 123, 255, 0.3)';
        if (t < 20) return 'rgba(40, 167, 69, 0.3)';
        if (t < 25) return 'rgba(255, 255, 255, 1)';
        if (t < 30) return 'rgba(253, 126, 20, 0.3)';
        return 'rgba(220, 53, 69, 0.3)';
    }

    google.charts.load('current', {'packages':['gauge']});
    let gaugeChart, gaugeData, gaugeOptions;

    google.charts.setOnLoadCallback(() => {
        gaugeData = google.visualization.arrayToDataTable([
            ['Label', 'Value'],
            ['V0', 0], ['V1', 0], ['V2', 0],
            ['V3', 0], ['V4', 0], ['V5', 0]
        ]);
        gaugeOptions = { width: 800, height: 160, redFrom: 90, redTo: 100, yellowFrom: 75, yellowTo: 90, minorTicks: 5 };
        gaugeChart = new google.visualization.Gauge(document.getElementById('gauges_div'));
        gaugeChart.draw(gaugeData, gaugeOptions);
    });

    const ctx = document.getElementById('historyChart').getContext('2d');
    const historyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [0,1,2,3,4,5].map(i => ({
                label: `V${i}`,
                data: [],
                borderColor: `hsl(${i * 60}, 70%, 50%)`,
                backgroundColor: `hsl(${i * 60}, 70%, 50%, 0.1)`,
                fill: true,
                tension: 0.4
            }))
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, max: 100 } } }
    });

    async function updateDashboard() {
        try {
            const response = await fetch('api_sensors.php');
            const data = await response.json();

            if (data.length > 0) {
                const last = data[data.length - 1];
                if (gaugeChart) {
                    for(let i=0; i<=5; i++) gaugeData.setValue(i, 1, parseFloat(last[`v${i}`]));
                    gaugeChart.draw(gaugeData, gaugeOptions);
                }
                for(let i=0; i<=5; i++) {
                    const val = parseFloat(last[`v${i}`]);
                    const roomEl = document.getElementById(`room-v${i}`);
                    const valEl = document.getElementById(`val-v${i}`);
                    valEl.innerText = val.toFixed(1) + '°C';
                    roomEl.style.backgroundColor = getTempColor(val);
                }
                historyChart.data.labels = data.map(row => row.recorded.split(' ')[1]);
                for(let i=0; i<=5; i++) historyChart.data.datasets[i].data = data.map(row => row[`v${i}`]);
                historyChart.update('none');
            }
        } catch (error) { console.error('Błąd:', error); }
    }

    document.getElementById('simulator-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btn-send');
        btn.disabled = true;
        try {
            await fetch('api_sensors.php', { method: 'POST', body: new FormData(e.target) });
            updateDashboard();
        } catch (error) {}
        setTimeout(() => { btn.disabled = false; }, 1000);
    });

    setInterval(updateDashboard, 2000);
    updateDashboard();
</script>