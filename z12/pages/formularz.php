<?php
/**
 * Plik: pages/formularz.php
 * Cel: Symulator sterownika SCADA (Panel sterowania wejściami).
 * Funkcjonalność: Umożliwia ręczne wprowadzanie danych do tabeli 'pomiary'.
 */
if (!isset($_SESSION['loggedin']) || ($_SESSION['app_id'] ?? '') !== 'lab12') {
    exit('Brak dostępu.');
}
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0"><i class="bi bi-broadcast"></i> Symulator Sterownika (Punkt 9d)</h5>
            </div>
            <div class="card-body p-4">
                <form id="simulatorForm">
                    <!-- SEKCJA TEMPERATUR -->
                    <h6 class="border-bottom pb-2 mb-3 text-primary"><i class="bi bi-thermometer-half"></i> Czujniki Temperatury</h6>
                    <div class="row g-3 mb-4">
                        <?php 
                        $labels = [
                            'x1' => 'Temp. zasilania (x1)', 
                            'x2' => 'Temp. powrotu (x2)', 
                            'x3' => 'Pokój 1 (x3)', 
                            'x4' => 'Pokój 2 (x4)', 
                            'x5' => 'Pokój 3 (x5)'
                        ];
                        foreach($labels as $id => $label): ?>
                        <div class="col-md-4 col-sm-6">
                            <label for="<?php echo $id; ?>" class="form-label small"><?php echo $label; ?></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="<?php echo $id; ?>" id="<?php echo $id; ?>" class="form-control" step="0.1" value="<?php echo rand(180, 240)/10; ?>" required>
                                <span class="input-group-text">°C</span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- SEKCJA AUTOMATYKI -->
                    <h6 class="border-bottom pb-2 mb-3 text-primary"><i class="bi bi-gear-fill"></i> Stany Automatyki i Alarmy</h6>
                    <div class="row g-3">
                        <!-- Wentylacja -->
                        <div class="col-md-6">
                            <label class="form-label small">Wentylacja</label>
                            <select name="ventilation" class="form-select form-select-sm" required>
                                <option value="0">0 - Wyłączona</option>
                                <option value="1">1 - Bieg wolny</option>
                                <option value="2">2 - Bieg szybki</option>
                            </select>
                        </div>
                        <!-- Alarmy (Selecty) -->
                        <?php 
                        $alarms = [
                            'fire_alarm' => ['label' => 'Pożar', 'icon' => 'bi-fire'],
                            'flood' => ['label' => 'Zalanie', 'icon' => 'bi-droplets'],
                            'gas' => ['label' => 'Ulatnianie gazu', 'icon' => 'bi-danger'],
                            'co2' => ['label' => 'Poziom CO2', 'icon' => 'bi-cloud-slash']
                        ];
                        foreach($alarms as $id => $info): ?>
                        <div class="col-md-6">
                            <label class="form-label small"><i class="<?php echo $info['icon']; ?>"></i> <?php echo $info['label']; ?></label>
                            <select name="<?php echo $id; ?>" class="form-select form-select-sm">
                                <option value="0" class="text-success">OK / System czuwa</option>
                                <option value="1" class="text-danger">ALARM!</option>
                            </select>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div id="statusMsg"></div>
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-send-fill"></i> Wyślij Dane do Systemu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('simulatorForm').onsubmit = async function(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type="submit"]');
    const status = document.getElementById('statusMsg');
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Wysyłanie...';
    
    try {
        const formData = new FormData(this);
        const response = await fetch('api_save.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.status === 'success') {
            status.innerHTML = '<div class="badge bg-success py-2 px-3"><i class="bi bi-check-circle"></i> Dane zapisane!</div>';
            // Lekka zmiana wartości dla następnego wysłania
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.value = (parseFloat(input.value) + (Math.random() - 0.5)).toFixed(1);
            });
        } else {
            status.innerHTML = '<div class="badge bg-danger py-2 px-3">Błąd: ' + result.message + '</div>';
        }
    } catch (err) {
        status.innerHTML = '<div class="badge bg-danger py-2 px-3">Błąd połączenia z API</div>';
    } finally {
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send-fill"></i> Wyślij Dane do Systemu';
        }, 1500);
    }
};
</script>